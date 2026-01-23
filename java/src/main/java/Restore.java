import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Arrays;
import java.util.List;
import java.util.concurrent.ExecutionException;

import com.algolia.api.SearchClient;
import com.algolia.model.search.*;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;

import io.github.cdimascio.dotenv.Dotenv;

public class Restore {
    
    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Restore.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/search#java
        try (SearchClient client = new SearchClient(appID, apiKey)) {

             ObjectMapper objectMapper = new ObjectMapper();

            // Restoring all records with replace all objects method
            // https://www.algolia.com/doc/api-reference/api-methods/replace-all-objects/

            // Read json file
            Iterable<Object> recordsList = objectMapper.readValue(new FileInputStream(indexName + "_records.json"), new TypeReference<Iterable<Object>>() {});

            // Restore Records
            client.replaceAllObjects(
                indexName,
                recordsList,
                77,
                Arrays.asList(ScopeType.SETTINGS, ScopeType.SYNONYMS)
            );

            // Restoring settings with set settings method
            // https://www.algolia.com/doc/api-reference/api-methods/set-settings/

            // Read json file
            IndexSettings settings = objectMapper.readValue(new File(indexName + "_settings.json"), IndexSettings.class);

            // Restore settings
            client.setSettings(
                indexName,
                settings,
                true
            );

            // Restoring Rules with replace all rules method
            // https://www.algolia.com/doc/api-reference/api-methods/replace-all-rules/

            // Read json file
            List<Rule> rulesList = objectMapper.readValue(new FileInputStream(indexName + "_rules.json"), new TypeReference<List<Rule>>() {});

            // Restore Rules
            UpdatedAtResponse rulesResp = client.saveRules(
                indexName,
                rulesList,
                true,
                false
            );

            client.waitForTask(indexName, rulesResp.getTaskID());

            // Restoring Synonyms with replace all synonyms method
            // https://www.algolia.com/doc/api-reference/api-methods/replace-all-synonyms/?client=php

            // Read json file
            List<SynonymHit> synonymsList = objectMapper.readValue(new FileInputStream(indexName + "_synonyms.json"), new TypeReference<List<SynonymHit>>() {});

            // Restore Synonyms
            UpdatedAtResponse synonymsResp = client.saveSynonyms(
                indexName,
                synonymsList,
                true,
                true
            );

            client.waitForTask(indexName, synonymsResp.getTaskID());
        }
    }
}