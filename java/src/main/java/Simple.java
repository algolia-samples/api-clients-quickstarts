import com.algolia.search.*;
import com.algolia.search.models.indexing.BatchIndexingResponse;
import com.algolia.search.models.indexing.Query;
import com.algolia.search.models.indexing.SearchResult;
import java.io.IOException;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ExecutionException;
import io.github.cdimascio.dotenv.Dotenv;

public class Simple {

    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Simple.run();
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
            
            // Add new objects to the index
            // https://www.algolia.com/doc/api-reference/api-methods/add-objects/
            Contact contact = new Contact("1", "Foo", null);
            CompletableFuture<BatchIndexingResponse> indexingFuture = index.saveObjectAsync(contact, true);

            // Wait for the indexing task to complete
            // https://www.algolia.com/doc/api-reference/api-methods/wait-task/
            indexingFuture.get();
            
            // Search the index for "Fo"
            // https://www.algolia.com/doc/api-reference/api-methods/search/
            CompletableFuture<SearchResult<Contact>> searchAlgoliaFuture =
                index.searchAsync(new Query("Foo"));

            System.out.println("Search results:" + searchAlgoliaFuture.get().getHits());
        }
    }
}