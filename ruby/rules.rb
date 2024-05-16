# Install the API client: https://www.algolia.com/doc/api-client/getting-started/install/ruby/?client=ruby
require 'dotenv/load'
require 'algolia'
# require 'date'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Start the API client
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/
client = Algolia::Search::Client.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create an index (or connect to it, if an index with the name `ALGOLIA_INDEX_NAME` already exists)
# https://www.algolia.com/doc/api-client/getting-started/instantiate-client-index/#initialize-an-index
index = client.init_index(ALGOLIA_INDEX_NAME)

# Exporting the rules 
# https://www.algolia.com/doc/api-reference/api-methods/export-rules/#examples
print "Original rules:\n"
all_rules = index.browse_rules()
all_rules.each { |rule| puts rule }
print "\n"

# Adding a new rule 
# https://www.algolia.com/doc/api-reference/api-methods/save-rule/?client=php
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
index.save_rule(rule)


# Save the Rule, and forward it to all replicas of the index.
# index.save_rule(rule, { forwardToReplicas: true })

print "#{rule_id} added successfully\n"
print "\n"

# Exporting the modified rules 
# https://www.algolia.com/doc/api-reference/api-methods/export-rules/
print "Modified rules:\n"
all_modified_rules = index.browse_rules()
all_modified_rules.each { |rule| puts rule }
print "\n"