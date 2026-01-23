import java.io.IOException;
import java.util.Arrays;
import java.util.concurrent.ExecutionException;

import com.algolia.api.SearchClient;
import com.algolia.model.search.*;

import io.github.cdimascio.dotenv.Dotenv;

public class Rules {
    
    public static void main(String[] args) throws ExecutionException, InterruptedException, IOException {
        Rules.run();
    }

    public static void run() throws ExecutionException, InterruptedException, IOException {
    
        Dotenv dotenv = Dotenv.configure().load();

        String appID = dotenv.get("ALGOLIA_APP_ID");
        String apiKey = dotenv.get("ALGOLIA_API_KEY");
        String indexName = dotenv.get("ALGOLIA_INDEX_NAME");

        // Start the API client
        // https://www.algolia.com/doc/libraries/sdk/methods/search#java
        try (SearchClient client = new SearchClient(appID, apiKey)) {
            // Exporting the rules 
            // https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
            System.out.println("Original rules:\n");
            Iterable<Rule> response = client.browseRules(indexName, new SearchRulesParams());

            for (Rule rule : response) {
                System.out.println("- Rule: " + rule.getObjectID());
            }

            String ruleID = "a-rule-id";

            Rule newRule = new Rule()
                    .setObjectID(ruleID)
                    .setConditions(Arrays.asList(new Condition().setPattern("flower").setAnchoring(Anchoring.CONTAINS)))
                    .setConsequence(new Consequence().setPromote(Arrays.asList(new PromoteObjectID().setObjectID("439957720").setPosition(0))));

            // Adding a new rule 
            // https://www.algolia.com/doc/api-reference/api-methods/save-rule/#save-a-rule
            UpdatedAtResponse saveRuleResp = client.saveRule(
                indexName,
                ruleID,
                newRule
            );

            client.waitForTask(indexName, saveRuleResp.getTaskID());

            // Exporting the modified rules 
            // https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
            System.out.println("Updated rules:\n");
            response = client.browseRules(indexName, new SearchRulesParams());

            for (Rule rule : response) {
                System.out.println("- Rule: " + rule.getObjectID());
            }

            client.close();
        }
    }
}
