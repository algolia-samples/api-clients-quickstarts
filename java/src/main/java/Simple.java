import com.algolia.api.SearchClient;
import com.algolia.model.search.*;
import java.io.IOException;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ExecutionException;
import io.github.cdimascio.dotenv.Dotenv;

public class Simple {

    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Simple.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
        try (SearchClient client = new SearchClient(appID, apiKey)) {

            // Add new objects to the index
            // https://www.algolia.com/doc/api-reference/api-methods/add-objects/
            Contact contact = new Contact("1", "Foo", Optional.empty());

            SaveObjectResponse saveResp = client.saveObject(
                indexName,
                contact
            );

            // Wait for the indexing task to complete
            // https://www.algolia.com/doc/api-reference/api-methods/wait-task/
            client.waitForTask(indexName, saveResp.getTaskID());
            
            // Search the index for "Fo"
            // https://www.algolia.com/doc/api-reference/api-methods/search/
            CompletableFuture<SearchResponse<Hit>> respSearch = client.searchSingleIndexAsync(indexName, Hit.class);

            System.out.println("Search results:" + respSearch.get().getHits());
        }
    }
}