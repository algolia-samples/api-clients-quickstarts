using System;
using System.IO;
using System.Linq;
using System.Text.Json;
using System.Collections.Generic;
using McMaster.Extensions.CommandLineUtils;
// Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/csharp/?client=csharp
using Algolia.Search.Clients;
using Algolia.Search.Http;
using Algolia.Search.Models.Search;
using Algolia.Search.Models.Analytics;
using dotenv.net;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

using Action = Algolia.Search.Models.Search.Action;
using JsonSerializer = Newtonsoft.Json.JsonSerializer;

namespace DotNetQuickStart
{
    class Program {

        // Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
        // and choose a name for your index. Add these environment variables to a `.env` file:
        // ALGOLIA_APP_ID, ALGOLIA_API_KEY, ALGOLIA_INDEX_NAME
        private static string _appId;
        private static string _apiKey;
        private static string _indexName;
        private static string _analyticsUrlDomain;

        public class Nutrition
        {
            public string Name { get; set; }
        }

        public static int Main(string[] args)
        {
            DotEnv.Load();

            InitKeys();

            var app = new CommandLineApplication
            {
                Name = "fake-npm",
                Description = "A fake version of the node package manager",
            };

            app.OnExecute(() =>
            {
                Console.WriteLine("Specify a sample to run. Ex: `dotenet run simple`");
                return 1;
            });

            app.Command("simple", configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/libraries/sdk/methods/search
                    var client = new SearchClient(_appId, _apiKey);

                    // Add new objects to the index
                    // https://www.algolia.com/doc/api-reference/api-methods/add-objects/
                    List<Contact> objs = new List<Contact>();
                    objs.Add(new Contact{ObjectID = "1", Name = "Foo"});

                    var response = await client.SaveObjectsAsync(_indexName, objs, true);

                    // Search the index for "Fo"
                    // https://www.algolia.com/doc/api-reference/api-methods/search/
                    var search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = "Foo"})
                    );
                    Console.WriteLine(search.Hits.ElementAt(0).ToString());

                    return 1;
                });
            });

            app.Command("indexing", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    var client = new SearchClient(_appId, _apiKey);

                    // We don't have any objects (yet) in our index
                    var search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = "Fo"})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Define some objects to add to our index
                    // https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
                    List<Contact> objs = new List<Contact>();
                    objs.Add(new Contact{ObjectID = "1", Name = "Foo"});
                    objs.Add(new Contact{ObjectID = "2", Name = "Bar"});

                    // Save Objects: Add mutliple new objects to an index.
                    // https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=csharp
                    Console.WriteLine("Save Objects - Adding multiple objects: [{0}]", string.Join(", ", objs));
                    await client.SaveObjectsAsync(_indexName, objs, true);

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Save Objects: Replace an existing object with an updated set of attributes.
                    // https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=csharp
                    Console.WriteLine("Save Objects - Replacing objects’s attributes on: {0}'", objs[0]);
                    List<Contact> changedContacts = new List<Contact>();
                    changedContacts.Add(new Contact{Name = "FooBar", ObjectID = "1"});

                    await client.SaveObjectsAsync(_indexName, changedContacts, true);

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Partial Update Objects: Update one or more attributes of an existing object.
                    // https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=csharp
                    Console.WriteLine("Save Objects - Updating object’s attributes on: {0}",  objs[0]);
                    Contact anotherChangedContact = new Contact{
                        Email = "foo@bar.com",
                        ObjectID = "1"
                    };
                    await client.PartialUpdateObjectAsync(_indexName, anotherChangedContact.ObjectID, anotherChangedContact);

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Delete Objects: Remove objects from an index using their objectID.
                    // https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=csharp
                    var objectIdToDelete = objs[0].ObjectID;
                    Console.WriteLine("Delete Objects - Deleting object with objectID: \"{0}\"",  objectIdToDelete);
                    await client.DeleteObjectAsync(_indexName, objectIdToDelete);

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
                    // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=csharp
                    List<Contact> newObjs = new List<Contact>();
                    newObjs.Add(new Contact{ObjectID = "1", Name = "NewFoo"});
                    newObjs.Add(new Contact{ObjectID = "2", Name = "NewBar"});
                    Console.WriteLine("Replace All Objects - Clears all objects and replaces them with: [{0}]",  string.Join(", ", newObjs));

                    await client.ReplaceAllObjectsAsync(
                            _indexName,
                            newObjs,
                            77,
                            new List<ScopeType> { Enum.Parse<ScopeType>("Settings"), Enum.Parse<ScopeType>("Synonyms") }
                    );

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Delete By: Remove all objects matching a filter (including geo filters).
                    // https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=csharp
                    Console.WriteLine("Delete By - Remove all objects matching \"name:NewBar\"");

                    // Firstly, have an attribute to filter on
                    // https://www.algolia.com/doc/api-client/methods/settings/?client=csharp
                    await client.SetSettingsAsync(
                        _indexName,
                        new IndexSettings
                        {
                            AttributesForFaceting = new List<string>{"name"}
                        },
                        true
                    );

                    await client.DeleteByAsync(
                        _indexName,
                        new DeleteByParams { Filters = "name:NewBar" }
                    );

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Get Objects: Get one or more objects using their objectIDs.
                    // https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=csharp
                    var objectId = newObjs[0].ObjectID;
                    Console.WriteLine("Get Objects - Getting object with objectID \"{0}\"", objectId);

                    var contact = await client.GetObjectAsync(
                        _indexName,
                        objectId,
                        new List<string> { "name" }
                    );
                    Console.WriteLine("Result: \"{0}\"", contact);

                    // Custom Batch: Perform several indexing operations in one API call.
                    // https://www.algolia.com/doc/api-reference/api-methods/batch/?client=csharp
                    var operations = new BatchParams
                        {
                            Requests = new List<MultipleBatchRequest>
                            {
                                new MultipleBatchRequest
                                {
                                    Action = Enum.Parse<Action>("AddObject"),
                                    IndexName = _indexName,
                                    Body = new Contact { Name = "BatchedBar" },
                                },
                                new MultipleBatchRequest
                                {
                                    Action = Enum.Parse<Action>("UpdateObject"),
                                    IndexName = _indexName,
                                    Body = new Contact { Name = "NewBatchedBar", ObjectID = objectId },
                                },
                            },
                        };
                    Console.WriteLine("Custom Batch - Batching {0} operations", operations.Requests.Count());

                    await client.MultipleBatchAsync(operations);

                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Clear Objects: Clear the records of an index without affecting its settings.
                    // https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=csharp
                    Console.WriteLine("Clear Objects: Clear the records of an index without affecting its settings.");

                    await client.ClearObjectsAsync(_indexName);

                    // We don't have any objects in our index
                    search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                    );
                    Console.WriteLine("Current objects: [{0}]", string.Join(", ", search.Hits));

                    return 1;
                });
            });

            app.Command("change-index-settings", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    var client = new SearchClient(_appId, _apiKey);

                   // Set settings
                   // https://www.algolia.com/doc/rest-api/search/set-settings
                   await client.SetSettingsAsync(
                        _indexName,
                        new IndexSettings
                        {
                            SearchableAttributes = new List<string>
                             {
                                    "actors",
                                    "genre",
                             },
                        },
                        true
                    );

                   // Print settings
                   Console.WriteLine("Index settings:\n");
                   var response = await client.GetSettingsAsync(_indexName, 2);
                   Console.WriteLine(response);

                return 1;
                });
            });

            app.Command("rules", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    var client = new SearchClient(_appId, _apiKey);

                   // Exporting the rules
                   // https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
                   Console.WriteLine("Original Rules:\n");

                   var results = await client.BrowseRulesAsync(_indexName, new SearchRulesParams {});

                   results.ToList().ForEach(rule => Console.WriteLine($"  - Rule: {rule.ObjectID}"));

                   // Add a new rule
                   // https://www.algolia.com/doc/api-reference/api-methods/save-rule/#save-a-rule

                   var newObjectId = "a-rule-id";
                   var rule = new Rule
                   {
                       ObjectID = newObjectId,
                            Conditions = new List<Condition>
                            {
                            new Condition { Pattern = "apple", Anchoring = Enum.Parse<Anchoring>("Contains") },
                            },
                            Consequence = new Consequence
                            {
                            Params = new ConsequenceParams { Filters = "brand:xiaomi" },
                            },
                   };

                   Console.WriteLine("Adding a new rule called: " + newObjectId);

                   await client.SaveRuleAsync(_indexName,newObjectId,rule);

                   // Exporting the modified rules
                   // https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
                   Console.WriteLine("Modified Rules:\n");

                   results = await client.BrowseRulesAsync(_indexName, new SearchRulesParams {});

                   results.ToList().ForEach(rule => Console.WriteLine($"  - Rule: {rule.ObjectID}"));

                   return 1;
                });
            });

            app.Command("backup", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    var client = new SearchClient(_appId, _apiKey);

                   // Get all records from an index
                   // https://www.algolia.com/doc/api-reference/api-methods/browse/#get-all-records-from-an-index
                   // Use an API key with `browse` ACL
                   Console.WriteLine("All the records:");
                   // Call the API
                   var records = await client.BrowseAsync<Hit>(_indexName);

                   // print the response
                   Console.WriteLine(records);
                   Console.WriteLine("\n");

                   // Transform records to array
                   Hit[] recordsArr = records.Hits.ToArray();

                   // Encode array to json
                   var jsonRecords = JsonConvert.SerializeObject(recordsArr);

                   // Write json to file
                   try
                    {
                        File.WriteAllText(_indexName + "_records.json", jsonRecords);
                        Console.WriteLine("JSON file created successfully...\n");
                    }
                    catch(Exception)
                    {
                        Console.WriteLine("An error has occurred while writing the records file.");
                    }

                   // Retrieve settings for an index
                   // https://www.algolia.com/doc/api-reference/api-methods/get-settings/#retrieve-settings-for-an-index
                   var settings = await client.GetSettingsAsync(_indexName, 2);
                   Console.WriteLine(settings);

                   // Encode array to json
                   var jsonSettings = JsonConvert.SerializeObject(settings);

                   // Write json to file
                    try
                    {
                        File.WriteAllText(_indexName + "_settings.json", jsonSettings);
                        Console.WriteLine("JSON file created successfully...\n");
                    }
                    catch(Exception)
                    {
                        Console.WriteLine("An error has occurred while writing the settings file.");
                    }

                   // Export rules
                   // https://www.algolia.com/doc/api-reference/api-methods/export-rules/
                   var rules = await client.BrowseRulesAsync(_indexName, new SearchRulesParams {});

                   rules.ToList().ForEach(rule => Console.WriteLine($"  - Rule: {rule.ObjectID}"));

                   // Encode array to json
                   var jsonRules = JsonConvert.SerializeObject(rules);

                   // Write json to file
                   try
                    {
                        File.WriteAllText(_indexName + "_rules.json", jsonRules);
                        Console.WriteLine("JSON file created successfully...\n");
                    }
                    catch(Exception)
                    {
                        Console.WriteLine("An error has occurred while writing the rules file.");
                    }

                   // Export synonyms
                   // https://www.algolia.com/doc/api-reference/api-methods/export-synonyms/
                   var synonyms = await client.BrowseSynonymsAsync(_indexName, new SearchSynonymsParams {});

                   synonyms.ToList().ForEach(synonym => Console.WriteLine($"  - Synonym: {synonym.ObjectID}"));

                   // Encode array to json
                   var jsonSynonyms = JsonConvert.SerializeObject(synonyms);

                   // Write json to file
                   try
                    {
                         File.WriteAllText(_indexName + "_synonyms.json", jsonSynonyms);
                        Console.WriteLine("JSON file created successfully...\n");
                    }
                    catch(Exception)
                    {
                        Console.WriteLine("An error has occurred while writing the synonyms file.");
                    }

                    return 1;
                });
            });

            app.Command("restore", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    var client = new SearchClient(_appId, _apiKey);

                   // Restoring all records with replace all objects method
                   // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/

                   // Read json file
                   Console.WriteLine("All the records:");
                   var records = File.ReadAllText(_indexName + "_records.json");
                   Console.WriteLine(records);
                   Console.WriteLine("\n");

                   // Decode json file
                   var arrayRecords = JsonConvert.DeserializeObject<Contact[]>(records);

                   // Restore records
                   await client.ReplaceAllObjectsAsync(
                            _indexName,
                            arrayRecords,
                            77
                   );

                   // Restoring settings with set settings method
                   // https://www.algolia.com/doc/api-reference/api-methods/set-settings/
                   
                   // Read json file
                   Console.WriteLine("All the settings:");
                   var settings = File.ReadAllText(_indexName + "_settings.json");
                   Console.WriteLine(settings);
                   Console.WriteLine("\n");

                   // Decode json file
                   var arraySettings = JsonConvert.DeserializeObject(settings);

                   // Restore settings
                    await client.SetSettingsAsync(
                        _indexName,
                        new IndexSettings
                        {
                            AttributesForFaceting = new List<string>{"name"}
                        },
                        true
                    );

                  // Restoring Rules with replace all rules method
                  // https://www.algolia.com/doc/rest-api/search/save-rules

                  // Read json file
                  Console.WriteLine("All the rules:");
                  var rules = File.ReadAllText(_indexName + "_rules.json");
                  Console.WriteLine(rules);
                  Console.WriteLine("\n");
                 
                  // Decode json file
                  var arrayRules = JsonConvert.DeserializeObject<List<Rule>>(rules);

                  // Restore rules
                  await client.SaveRulesAsync(_indexName, arrayRules, false, true);

                   // Restoring Synonyms with replace all synonyms method
                  // https://www.algolia.com/doc/api-reference/api-methods/replace-all-synonyms/?client=php

                   // Read json file
                  Console.WriteLine("All the synonyms:");
                  var synonyms = File.ReadAllText(_indexName + "_synonyms.json");
                  Console.WriteLine(synonyms);
                  Console.WriteLine("\n");

                   // Decode json file
                   var arraySynonyms = JsonConvert.DeserializeObject<List<SynonymHit>>(synonyms);

                   // Restore synonyms
                   await client.SaveSynonymsAsync(_indexName, arraySynonyms, false, true);

                   return 1;
                });
            });

            app.Command("return-top-hits", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                   // Start the Analytics client
                   // https://www.algolia.com/doc/libraries/sdk/methods/analytics
                   var client = new AnalyticsClient(new AnalyticsConfig(_appId, _apiKey, "us"));

                   // Request 1000 Top Searches
                   var response = await client.GetTopSearchesAsync(_indexName);

                   Console.WriteLine(response);

                   // Format the resulting json string
                   var json = JsonConvert.SerializeObject(response);

                   // Write json to file
                   try
                    {
                        File.WriteAllText(_indexName + "_top_results.json", json);
                        Console.WriteLine("JSON file created successfully...\n");
                    }
                    catch(Exception)
                    {
                        Console.WriteLine("An error has occurred while writing the records file.");
                    }

                    return 1;
                });
            });

            app.Command("generate-key", async configCmd =>
            {
                configCmd.OnExecuteAsync(async success =>
                {
                    // Start the API client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    var client = new SearchClient(_appId, _apiKey);

                   // Set permissions for API key
                   // https://www.algolia.com/doc/api-reference/api-methods/add-api-key/#method-param-acl
                    List<Acl> permissions = new List<Acl>();
                    permissions.Add(Enum.Parse<Acl>("Search"));
                    permissions.Add(Enum.Parse<Acl>("AddObject"));

                   // Create a new restricted search-only API key
                    var response = await client.AddApiKeyAsync(
                        new ApiKey
                        {
                            Acl = permissions,
                            Description = "my new api key",
                        }
                    );

                   var newKey = response.Key.ToString();

                   // Test the created key
                   Console.WriteLine("Testing key...\n");

                   // Initialise a new client with the generated key
                   client = new SearchClient(_appId, newKey);

                   // Test the new generated key by performing a search
                   try
                   {
                      var search = await client.SearchSingleIndexAsync<Hit>(
                        _indexName,
                        new SearchParams(new SearchParamsObject { Query = ""})
                        );
                        Console.WriteLine(search.Hits.ElementAt(0).ToString());  
                   }
                  catch
                   {
                        Console.WriteLine("An error occurred");
                   }

                    return 1;
                });
            });

            return app.Execute(args);
        }

        static void InitKeys()
        {
            if (string.IsNullOrEmpty(Environment.GetEnvironmentVariable("ALGOLIA_APP_ID")))
            {
                Console.WriteLine("Please set the following environment variable : ALGOLIA_APP_ID");
                Environment.Exit(1);
            }

            if (string.IsNullOrEmpty(Environment.GetEnvironmentVariable("ALGOLIA_API_KEY")))
            {
                Console.WriteLine("Please set the following environment variable : ALGOLIA_API_KEY");
                Environment.Exit(1);
            }

            if (string.IsNullOrEmpty(Environment.GetEnvironmentVariable("ALGOLIA_INDEX_NAME")))
            {
                Console.WriteLine("Please set the following environment variable : ALGOLIA_INDEX_NAME");
                Environment.Exit(1);
            }

            if (string.IsNullOrEmpty(Environment.GetEnvironmentVariable("ALGOLIA_ANALYTICS_URL")))
            {
                Console.WriteLine("Please set the following environment variable : ALGOLIA_ANALYTICS_URL");
                Environment.Exit(1);
            }

            _appId = Environment.GetEnvironmentVariable("ALGOLIA_APP_ID");
            _apiKey = Environment.GetEnvironmentVariable("ALGOLIA_API_KEY");
            _indexName = Environment.GetEnvironmentVariable("ALGOLIA_INDEX_NAME");

            // The Analytics API can be reached from multiple domains, each specific to a region. 
            // You should use the domain that matches the region where your analytics data is stored and processed. 
            // View your analytics region: https://www.algolia.com/infra/analytics
            // The following domains are available:
            // United States: https://analytics.us.algolia.com
            // Europe (Germany): https://analytics.de.algolia.com

            _analyticsUrlDomain = Environment.GetEnvironmentVariable("ALGOLIA_ANALYTICS_URL");
        }
    }
}
