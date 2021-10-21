import com.algolia.search.*;
import com.algolia.search.models.indexing.BatchIndexingResponse;
import com.algolia.search.models.indexing.Query;
import com.algolia.search.models.indexing.SearchResult;
import com.algolia.search.models.settings.IndexSettings;
import com.algolia.search.models.indexing.BatchOperation;
import com.algolia.search.models.indexing.BatchRequest;
import com.algolia.search.models.indexing.ActionEnum;

import java.io.IOException;
import java.lang.StackWalker.Option;
import java.util.List;
import java.util.Arrays;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ExecutionException;
import io.github.cdimascio.dotenv.Dotenv;

public class Indexing {

    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Indexing.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        // Start the API client
        // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
        try (SearchClient searchClient =
            DefaultSearchClient.create(dotenv.get("ALGOLIA_APP_ID"), dotenv.get("ALGOLIA_API_KEY"))) {

            // Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
            // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
            SearchIndex<Contact> index = searchClient.initIndex(dotenv.get("ALGOLIA_INDEX_NAME"), Contact.class);
            
            // Define some objects to add to our index
            // https://www.algolia.com/doc/api-client/methods/indexing/#object-and-record
            List<Contact> contacts = Arrays.asList(
                new Contact("1", "Foo", Optional.empty()),
                new Contact("2", "Bar", Optional.empty())
            );

            // We don't have any objects (yet) in our index
            SearchResult<Contact> searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());
            
            // Save Objects: Add mutliple new objects to an index.
            // https://www.algolia.com/doc/api-reference/api-methods/add-objects/?client=java
            System.out.println("Save Objects - Adding multiple objects: " + contacts);
            index.saveObjects(contacts, true).waitTask();
            
            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Save Objects: Replace an existing object with an updated set of attributes.
            // https://www.algolia.com/doc/api-reference/api-methods/save-objects/?client=java
            System.out.println("Save Objects - Replacing objects’s attributes on: " + contacts.get(0));
            Contact firstContact = contacts.get(0).setName("FooBar");
            index.saveObject(firstContact).waitTask();

            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Partial Update Objects: Update one or more attributes of an existing object.
            // https://www.algolia.com/doc/api-reference/api-methods/partial-update-objects/?client=java
            System.out.println("Save Objects - Updating object’s attributes on: " + contacts.get(0));
            firstContact.setEmail("test@test.com");
            index.partialUpdateObject(firstContact).waitTask();

            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Delete Objects: Remove objects from an index using their objectID.
            // https://www.algolia.com/doc/api-reference/api-methods/delete-objects/?client=java
            String objectIDToDelete = contacts.get(0).getObjectID();
            System.out.println("Delete Objects - Deleting object with objectID: " + objectIDToDelete);
            index.deleteObject(objectIDToDelete).waitTask();

            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Replace All Objects: Clears all objects from your index and replaces them with a new set of objects.
            // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/?client=java
            List<Contact> newContacts = Arrays.asList(
                new Contact("3", "NewFoo", Optional.empty()),
                new Contact("4", "NewBar", Optional.empty())
            );
            System.out.println("Replace All Objects - Clears all objects and replaces them with: " + newContacts);
            index.replaceAllObjects(newContacts, true);

            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Delete By: Remove all objects matching a filter (including geo filters).
            // https://www.algolia.com/doc/api-reference/api-methods/delete-by/?client=java
            System.out.println("Delete By - Remove all objects matching 'name:NewBar'");

            // Firstly, have an attribute to filter on
            // https://www.algolia.com/doc/api-client/methods/settings/?client=java
            IndexSettings settings = new IndexSettings();
            settings.setAttributesForFaceting(Arrays.asList("name"));
            index.setSettings(settings).waitTask();

            // Now delete the records matching "name=NewBar"
            index.deleteBy(new Query("").setFacetFilters(Arrays.asList(Arrays.asList("name:NewBar"))));

            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Get Objects: Get one or more objects using their objectIDs.
            // https://www.algolia.com/doc/api-reference/api-methods/get-objects/?client=java
            String objectIDToRetrieve = newContacts.get(0).getObjectID();
            System.out.println("Get Objects - Getting object with objectID: " + objectIDToRetrieve);

            Contact contact = index.getObject(objectIDToRetrieve);
            System.out.println("Result: " + contact);

            // Custom Batch: Perform several indexing operations in one API call.
            // https://www.algolia.com/doc/api-reference/api-methods/batch/?client=java
            List<BatchOperation<Contact>> operations = Arrays.asList(
                new BatchOperation<>(dotenv.get("ALGOLIA_INDEX_NAME"), ActionEnum.ADD_OBJECT, new Contact("3", "BatchedBar", Optional.empty())),
                new BatchOperation<>(dotenv.get("ALGOLIA_INDEX_NAME"), ActionEnum.UPDATE_OBJECT, new Contact("4", "BatchedFoo", Optional.empty()))
            );
            System.out.println("Custom Batch");
            searchClient.multipleBatch(operations).waitTask();
            
            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());

            // Clear Objects: Clear the records of an index without affecting its settings.
            // https://www.algolia.com/doc/api-reference/api-methods/clear-objects/?client=java
            System.out.println("Clear objects");
            index.clearObjects().waitTask();

            searchResults = index.search(new Query(""));
            System.out.println("Current objects: " + searchResults.getHits());
        }
    }
}