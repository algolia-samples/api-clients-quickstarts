using System;
using System.Linq;
using System.Collections.Generic;
using McMaster.Extensions.CommandLineUtils;
using Algolia.Search.Clients;
using Algolia.Search.Models.Search;
using Algolia.Search.Models.Settings;
using Algolia.Search.Models.Batch;
using Algolia.Search.Models.Enums;
using dotenv.net;

namespace DotNetQuickStart
{
    class Program {
        private static string _appId;
        private static string _apiKey;
        private static string _indexName;

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
                configCmd.OnExecute(() =>
                {
                    // Initialize the client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    SearchClient client = new SearchClient(_appId, _apiKey);

                    // Initialize an index
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
                    SearchIndex index = client.InitIndex(_indexName);

                    // Save Objects: Add mutliple new objects to an index.
                    // https://www.algolia.com/doc/api-reference/api-methods/add-objects/
                    List<Contact> objs = new List<Contact>();
                    objs.Add(new Contact{ObjectID = "1", Name = "Foo"});
                    var res = index.SaveObjects(objs);

                    // Waiting for the indexing task to complete
                    // https://www.algolia.com/doc/api-reference/api-methods/wait-task/
                    res.Wait();

                    // Search Index: Method used for querying an index.
                    // https://www.algolia.com/doc/api-reference/api-methods/search/
                    var search = index.Search<Contact>(new Query("Fo"));
                    Console.WriteLine(search.Hits.ElementAt(0).ToString());

                    return 1;
                });
            });

            app.Command("indexing", configCmd =>
            {
                configCmd.OnExecute(() =>
                {
                    // Initialize the client
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
                    SearchClient client = new SearchClient(_appId, _apiKey);

                    // Initialize an index
                    // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
                    SearchIndex index = client.InitIndex(_indexName);

                    // We don't have any objects (yet) in our index
                    var search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Define some objects to add to our index
                    // https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
                    List<Contact> objs = new List<Contact>();
                    objs.Add(new Contact{ObjectID = "1", Name = "Foo"});
                    objs.Add(new Contact{ObjectID = "2", Name = "Bar"});

                    // Save Objects: Add mutliple new objects to an index.
                    // https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=csharp
                    Console.WriteLine("Save Objects - Adding multiple objects: [{0}]", string.Join(", ", objs));
                    index.SaveObjects(objs).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Save Objects: Replace an existing object with an updated set of attributes.
                    // https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=csharp
                    Console.WriteLine("Save Objects - Replacing objects’s attributes on: {0}'", objs[0]);
                    Contact changedContact = new Contact {
                        Name = "FooBar",
                        ObjectID = "1"
                    };
                    index.SaveObject(changedContact).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Partial Update Objects: Update one or more attributes of an existing object.
                    // https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=csharp
                    Console.WriteLine("Save Objects - Updating object’s attributes on: {0}",  objs[0]);
                    Contact anotherChangedContact = new Contact{
                        Email = "foo@bar.com",
                        ObjectID = "1"
                    };
                    index.PartialUpdateObject(anotherChangedContact).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Delete Objects: Remove objects from an index using their objectID.
                    // https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=csharp
                    var objectIdToDelete = objs[0].ObjectID;
                    Console.WriteLine("Delete Objects - Deleting object with objectID: \"{0}\"",  objectIdToDelete);
                    index.DeleteObject(objectIdToDelete).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
                    // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=csharp
                    List<Contact> newObjs = new List<Contact>();
                    newObjs.Add(new Contact{ObjectID = "1", Name = "NewFoo"});
                    newObjs.Add(new Contact{ObjectID = "2", Name = "NewBar"});
                    Console.WriteLine("Replace All Objects - Clears all objects and replaces them with: [{0}]",  string.Join(", ", newObjs));
                    index.ReplaceAllObjects(newObjs).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Delete By: Remove all objects matching a filter (including geo filters).
                    // https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=csharp
                    Console.WriteLine("Delete By - Remove all objects matching \"name:NewBar\"");

                    // Firstly, have an attribute to filter on
                    // https://www.algolia.com/doc/api-client/methods/settings/?client=csharp
                    index.SetSettings(new IndexSettings {
                        AttributesForFaceting = new List<string>{"name"}
                    }).Wait();
                    
                    index.DeleteBy(new Query {
                        FacetFilters = new List<List<string>>{ new List<string> {"name:NewBar"} } // https://www.algolia.com/doc/api-reference/api-parameters/facetFilters/
                    }).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Get Objects: Get one or more objects using their objectIDs.
                    // https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=csharp
                    var objectId = newObjs[0].ObjectID;
                    Console.WriteLine("Get Objects - Getting object with objectID \"{0}\"", objectId);

                    var contact = index.GetObject<Contact>(objectId);
                    Console.WriteLine("Result: \"{0}\"", contact);

                    // Custom Batch: Perform several indexing operations in one API call.
                    // https://www.algolia.com/doc/api-reference/api-methods/batch/?client=csharp
                    List<BatchOperation<Contact>> operations = new List<BatchOperation<Contact>>
                    {
                        new BatchOperation<Contact>
                        {
                            Action = BatchActionType.AddObject,
                            IndexName = _indexName,
                            Body = new Contact { Name = "BatchedBar" }
                        },
                        new BatchOperation<Contact>
                        {
                            Action = BatchActionType.UpdateObject,
                            IndexName = _indexName,
                            Body = new Contact { Name = "NewBatchedBar", ObjectID = objectId }
                        }
                    };
                    Console.WriteLine("Custom Batch - Batching {0} operations", operations.Count());
                    client.MultipleBatch(operations).Wait();

                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]\n", string.Join(", ", search.Hits));

                    // Clear Objects: Clear the records of an index without affecting its settings.
                    // https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=csharp
                    Console.WriteLine("Clear Objects: Clear the records of an index without affecting its settings.");
                    index.ClearObjects().Wait();

                    // We don't have any objects in our index
                    search = index.Search<Contact>(new Query(""));
                    Console.WriteLine("Current objects: [{0}]", string.Join(", ", search.Hits));

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

            _appId = Environment.GetEnvironmentVariable("ALGOLIA_APP_ID");
            _apiKey = Environment.GetEnvironmentVariable("ALGOLIA_API_KEY");
            _indexName = Environment.GetEnvironmentVariable("ALGOLIA_INDEX_NAME");
        }
    }
}
