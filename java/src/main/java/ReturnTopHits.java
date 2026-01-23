import java.io.File;
import java.io.IOException;
import java.util.concurrent.ExecutionException;

import com.algolia.api.AnalyticsClient;
import com.algolia.config.*;
import com.algolia.model.analytics.GetTopSearchesResponse;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.SerializationFeature;

import io.github.cdimascio.dotenv.Dotenv;

public class ReturnTopHits {
    
    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        ReturnTopHits.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        // Pick a region for your analytics
        // us for the United States or de for Europe

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");
        String analyticsRegion = "us";

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/analytics
        // Initialize the client with your application region, eg. analytics.ALGOLIA_APPLICATION_REGION
        try (AnalyticsClient client = new AnalyticsClient(appID, apiKey, analyticsRegion)) {
            // Initialize ObjectMapper
            ObjectMapper objectMapper = new ObjectMapper();
            objectMapper.enable(SerializationFeature.INDENT_OUTPUT);

            RequestOptions options = new RequestOptions();
            options.addExtraQueryParameters("limit",1000);
            GetTopSearchesResponse topSearchResp = client.getTopSearches(
                indexName,
                null,   // clickAnalytics
                null,   // revenueAnalytics
                null,   // startDate
                null,   // endDate
                null,   // orderBy
                null,   // direction
                1000,
                null,
                null    // tags
            );

            System.out.println(topSearchResp);

            File recordsFile = new File(indexName + "__top_1000_searches.json");
            objectMapper.writeValue(recordsFile, topSearchResp); 
        }
    }
}