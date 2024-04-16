require 'algolia'
require 'dotenv/load'
require 'json'

# Algolia client credentials
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialize the client and the index
# https://www.algolia.com/doc/api-client/getting-started/initialize/ruby/?client=ruby#initialize-the-search-client
client = Algolia::Search::Client.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)
index = client.init_index(ALGOLIA_INDEX_NAME)

# Create a rule
# https://www.algolia.com/doc/api-reference/api-methods/save-rule/?client=ruby
puts "Creating a rule ..."
rule = {
     objectID: 'a-rule-id',
     conditions: [{
       pattern: 'Jimmie',
       anchoring: 'is'
     }],
     consequence: {
       params: {
         filters: "zip_code = 12345"
       }
     }
   }
   
# Optionally, to disable the rule
rule['enabled'] = false
   
# Optionally, to add validity time ranges
rule['validity'] = [
{
     from: Time.now.to_i,
     until: (DateTime.now + 10).to_time.to_i,
}
]

# Save the Rule.
index.save_rule(rule)
# Save the Rule and wait the end of indexing.
index.save_rule!(rule)
# Save the Rule, and forward it to all replicas of the index.
response = index.save_rule(rule, { forwardToReplicas: true })


# Browse rules
# https://www.algolia.com/doc/api-reference/api-methods/export-rules/
res = index.browse_rules()

# Store rules into an array
rules = res.map {|rule| rule} 

if rules.empty?
     puts "No rules are configured for your index yet."
else
     # Create the json file with all rules
     path = "#{ALGOLIA_INDEX_NAME}_rules.json"
     File.open(path, 'wb') do |file|
          file.write(JSON.generate(rules))
     end    
     puts "Rules export completed"
end

