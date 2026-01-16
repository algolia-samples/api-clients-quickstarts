# Install the API client: https://www.algolia.com/doc/libraries/sdk/install#ruby
require 'dotenv/load'
require 'algolia'
# require 'date'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialise the client and connect to it
# https://www.algolia.com/doc/libraries/sdk/methods/search#ruby
client = Algolia::SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)


# Exporting the rules 
# https://www.algolia.com/doc/libraries/sdk/methods/search/browse-rules
print "Original rules:\n"
all_rules = client.browse_rules(ALGOLIA_INDEX_NAME)
all_rules.each { |rule| puts rule }
print "\n"

# Adding a new rule 
# https://www.algolia.com/doc/libraries/sdk/methods/search/save-rule
rule_id = 'a-rule-id'
print "Adding new rule: #{rule_id}\n"

rule = {
  objectID: rule_id,
  conditions: [{
    pattern: 'dress',
    anchoring: 'contains',
    # Uncomment line 38 if the pattern should match plurals, synonyms, and typos.
    # alternatives: true
  }],
  consequence: {
    params: {
      filters: 'subCategory:Dress'
    }
  }
}

# Uncomment line 48 to turn the rule off
# rule['enabled'] = false

# Uncomment lines 51 - 56 to add valid time ranges (also uncomment `require 'date'` on line 4)
# rule['validity'] = [
#   {
#     from: Time.now.to_i,
#     until: (DateTime.now + 10).to_time.to_i,
#   }
# ]

# Save the Rule.
response = client.save_rule(ALGOLIA_INDEX_NAME, rule_id, rule)

# Wait for task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task#ruby
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

# Save the Rule, and forward it to all replicas of the index.
# client.save_rule(ALGOLIA_INDEX_NAME, rule, { forwardToReplicas: true })

print "#{rule_id} added successfully\n"
print "\n"

# Exporting the modified rules 
# https://www.algolia.com/doc/libraries/sdk/methods/search/browse-rules
print "Modified rules:\n"
all_modified_rules = client.browse_rules(ALGOLIA_INDEX_NAME)
all_modified_rules.each { |rule| puts rule }
print "\n"