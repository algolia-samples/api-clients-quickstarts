import java.io.IOException;
import java.util.Arrays;
import java.util.concurrent.ExecutionException;

import com.algolia.api.SearchClient;
import com.algolia.model.search.*;

import io.github.cdimascio.dotenv.Dotenv;

public class GenerateKey {
    
    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        GenerateKey.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/search#java
        try (SearchClient client = new SearchClient(appID, apiKey)) {
            
            // Create a new restricted search-only API key
            AddApiKeyResponse apiKeyResp = client.addApiKey(
                new ApiKey()
                .setAcl(Arrays.asList(Acl.SEARCH, Acl.ADD_OBJECT))
                .setDescription("Restricted search-only API key for algolia.com")
                .setMaxQueriesPerIPPerHour(100)
            );

            client.waitForApiKey(apiKeyResp.getKey(), ApiKeyOperation.ADD);

            // Make sure new key has been created
            String newKey = apiKeyResp.getKey();

            if (newKey == apiKeyResp.getKey()) {
                System.out.println("Key generated successfully: " + newKey);
            } else {
                System.out.println("Error while creating key\n");
            }

            // Test the created key
            System.out.println("Testing key");

            // Initialise a new client with the generated key
            SearchClient newClient = new SearchClient(appID, newKey);

            // Test the new generated key by performing a search
            try {
                SearchResponse<Hit> newKeySearchResp = newClient.searchSingleIndex(indexName, Hit.class);
                System.out.println(newKeySearchResp.getHits());
            }
            catch(Exception e) {
                System.out.println("Failed search with the new key\n");
            }

            newClient.close();
        }
    }
}