import java.io.IOException;
import java.util.Arrays;
import java.util.concurrent.ExecutionException;

import com.algolia.api.SearchClient;
import com.algolia.model.search.*;

import io.github.cdimascio.dotenv.Dotenv;

public class Settings {
    
    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Settings.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/search#java
        try (SearchClient client = new SearchClient(appID, apiKey)) {
            // Set index settings
            // https://www.algolia.com/doc/api-reference/api-methods/set-settings/
            IndexSettings settings = new IndexSettings();
            settings.setSearchableAttributes(Arrays.asList("actors","genre")).setCustomRanking(Arrays.asList("desc(rating)"));

            UpdatedAtResponse response = client.setSettings(
                indexName,
                settings,
                true
            );

            client.waitForTask(indexName, response.getTaskID());

            // Printing settings 
            // https://www.algolia.com/doc/api-reference/api-methods/get-settings/
            SettingsResponse settingsResp = client.getSettings(indexName, 2);

            // print the response
            System.out.println(settingsResp);
        }
    }
}
