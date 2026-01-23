
import java.io.IOException;
import java.util.Arrays;
import java.util.List;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ExecutionException;

import com.algolia.api.SearchClient;
import com.algolia.model.search.*;

import io.github.cdimascio.dotenv.Dotenv;

public class Indexing {

    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Indexing.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/search#java
        try (SearchClient client = new SearchClient(appID, apiKey)) {
            
            // Define some objects to add to our index
            // https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
            List<Contact> contacts = Arrays.asList(
                new Contact("1", "Foo", Optional.empty()),
                new Contact("2", "Bar", Optional.empty())
            );

            // We don't have any objects (yet) in our index
            CompletableFuture<SearchResponse<Hit>> searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());
            
            // Save Objects: Add mutliple new objects to an index.
            // https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=java
            System.out.println("Save Objects - Adding multiple objects: " + contacts);

            List<BatchResponse> saveObjects = client.saveObjects(indexName,contacts);

            client.waitForTask(indexName, saveObjects.get(0).getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Save Objects: Replace an existing object with an updated set of attributes.
            // https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=java
            System.out.println("Save Objects - Replacing objects’s attributes on: " + contacts.get(0));
            Contact firstContact = contacts.get(0).setName("FooBar");
            SaveObjectResponse saveObj = client.saveObject(indexName,firstContact);

            client.waitForTask(indexName, saveObj.getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Partial Update Objects: Update one or more attributes of an existing object.
            // https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=java
            System.out.println("Save Objects - Updating object’s attributes on: " + contacts.get(0));
            firstContact.setEmail("test@test.com");

            UpdatedAtWithObjectIdResponse partialUpdateResp = client.partialUpdateObject(
                indexName,
                firstContact.getObjectID(),
                firstContact
            );

            client.waitForTask(indexName, partialUpdateResp.getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Delete Objects: Remove objects from an index using their objectID.
            // https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=java
            String objectIDToDelete = contacts.get(0).getObjectID();
            System.out.println("Delete Objects - Deleting object with objectID: " + objectIDToDelete);
            List<BatchResponse> deleteObjResp = client.deleteObjects(indexName, Arrays.asList(objectIDToDelete));

            client.waitForTask(indexName, deleteObjResp.get(0).getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
            // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=java
            List<Contact> newContacts = Arrays.asList(
                new Contact("3", "NewFoo", Optional.empty()),
                new Contact("4", "NewBar", Optional.empty())
            );
            System.out.println("Replace All Objects - Clears all objects and replaces them with: " + newContacts);
            ReplaceAllObjectsResponse replaceAllObjResp = client.replaceAllObjects(
                indexName,
                newContacts,
                2,
                Arrays.asList(ScopeType.SETTINGS, ScopeType.SYNONYMS)
            );

            client.waitForTask(indexName, replaceAllObjResp.getBatchResponses().get(0).getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Delete By: Remove all objects matching a filter (including geo filters).
            // https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=java
            System.out.println("Delete By - Remove all objects matching 'name:NewBar'");

            // Firstly, have an attribute to filter on
            // https://www.algolia.com/doc/api-client/methods/settings/?client=java
            IndexSettings settings = new IndexSettings();
            settings.setAttributesForFaceting(Arrays.asList("name"));

            UpdatedAtResponse response = client.setSettings(
                indexName,
                settings,
                true
            );

            client.waitForTask(indexName, response.getTaskID());

            // Now delete the records matching "name=NewBar"
            response = client.deleteBy(indexName, new DeleteByParams().setFilters("name:NewBar"));

            client.waitForTask(indexName, response.getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Get Objects: Get one or more objects using their objectIDs.
            // https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=java
            String objectIDToRetrieve = newContacts.get(0).getObjectID();
            System.out.println("Get Objects - Getting object with objectID: " + objectIDToRetrieve);

            Object getObjResp = client.getObject(indexName, objectIDToRetrieve);
            System.out.println("Result: " + getObjResp);

            // Custom Batch: Perform several indexing operations in one API call.
            // https://www.algolia.com/doc/api-reference/api-methods/batch/?client=java
            BatchWriteParams operations = new BatchWriteParams().setRequests(
                    Arrays.asList(
                    new BatchRequest()
                        .setAction(Action.ADD_OBJECT)
                        .setBody(
                            new Contact("3", "BatchedBar", Optional.empty())
                        ),
                    new BatchRequest()
                        .setAction(Action.ADD_OBJECT)
                        .setBody(
                            new Contact("4", "BatchedFoo", Optional.empty())
                        )
                    )
                );

            BatchResponse batchResp = client.batch(
                indexName,
                operations
            );

            System.out.println("Custom Batch");
            client.waitForTask(indexName, batchResp.getTaskID());
            
            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());

            // Clear Objects: Clear the records of an index without affecting its settings.
            // https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=java
            System.out.println("Clear objects");
            response = client.clearObjects(indexName);

            client.waitForTask(indexName, response.getTaskID());

            searchResults = client.searchSingleIndexAsync(indexName, Hit.class);
            System.out.println("Current objects: " + searchResults.get().getHits());
        
        }
    }
}