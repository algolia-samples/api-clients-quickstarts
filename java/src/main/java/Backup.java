import java.io.File;
import java.io.IOException;
import java.util.concurrent.ExecutionException;

import com.algolia.api.SearchClient;
import com.algolia.model.search.*;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.SerializationFeature;
import com.fasterxml.jackson.databind.node.ObjectNode;

import io.github.cdimascio.dotenv.Dotenv;

public class Backup {
    
    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Backup.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/search#java
        try (SearchClient client = new SearchClient(appID, apiKey)) {

            // Initialize ObjectMapper
            ObjectMapper objectMapper = new ObjectMapper();
            objectMapper.enable(SerializationFeature.INDENT_OUTPUT);

            // Get all records from an index
            // https://www.algolia.com/doc/api-reference/api-methods/browse/#get-all-records-from-an-index
            // Use an API key with `browse` ACL
            BrowseResponse<Hit> respRecords = client.browse(indexName, Hit.class);

            // Write records to file
            File recordsFile = new File(indexName + "_records.json");
            objectMapper.writeValue(recordsFile, respRecords.getHits());      

            // Retrieve settings for an index
            // https://www.algolia.com/doc/api-reference/api-methods/get-settings/#retrieve-settings-for-an-index

            // Export settings
            SettingsResponse respSettings = client.getSettings(indexName, 2);

            // Write settings to file
            File settingsFile = new File(indexName + "_settings.json");
            objectMapper.writeValue(settingsFile, respSettings);

            // The following settings will export as null if the default value is used and the setting hasn't been modified
            // typoTolerance, ignorePlurals, removeStopWords, distinct, reRankingApplyFilter, primary
            // Remove these settings if the values are null to prevent errors while restoring

            //Get the file
            ObjectNode settingsFileContent = (ObjectNode) objectMapper.readTree(settingsFile);

            // Create array of settings
            String[] settingsValues = {"typoTolerance", "ignorePlurals", "removeStopWords", "distinct", "reRankingApplyFilter", "primary"};

            for (int i = 0; i < settingsValues.length; i++) {
                if (settingsFileContent.has(settingsValues[i]) && settingsFileContent.get(settingsValues[i]).isNull()) {
                    settingsFileContent.remove(settingsValues[i]);
                }
            }

            objectMapper.writeValue(settingsFile, settingsFileContent);

            // Export rules 
            // https://www.algolia.com/doc/api-reference/api-methods/export-rules/
            Iterable<Rule> respRules = client.browseRules(indexName, new SearchRulesParams());

            // Transform rules iterator to Array and then to JSON
            File rulesFile = new File(indexName + "_rules.json");
            objectMapper.writeValue(rulesFile, respRules);        

            // Export synonyms
            // https://www.algolia.com/doc/api-reference/api-methods/export-synonyms/
            Iterable<SynonymHit> respSynonyms = client.browseSynonyms(indexName, new SearchSynonymsParams());

            // Transform synonyms iterator to Array and then to JSON
            File synonymsFile = new File(indexName + "_synonyms.json");
            objectMapper.writeValue(synonymsFile, respSynonyms);  

        }
    }
}
