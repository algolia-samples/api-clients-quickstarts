# Install the API client: https://www.algolia.com/doc/libraries/sdk/install#ruby
require 'algolia'
require 'dotenv/load'
require 'json'

# Get your Algolia Application ID and (admin) API key from the dashboard: https://www.algolia.com/account/api-keys
# and choose a name for your index. Add these environment variables to a `.env` file:
ALGOLIA_APP_ID = ENV['ALGOLIA_APP_ID']
ALGOLIA_API_KEY = ENV['ALGOLIA_API_KEY']
ALGOLIA_INDEX_NAME = ENV['ALGOLIA_INDEX_NAME']

# Initialise the client and connect to it
# https://www.algolia.com/doc/libraries/sdk/methods/search#ruby
client = Algolia::SearchClient.create(ALGOLIA_APP_ID, ALGOLIA_API_KEY)

# Create a rule

puts "Creating a rule ..."
objectID = 'a-rule-id'
rule = {
     objectID: objectID,
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
# https://www.algolia.com/doc/libraries/sdk/methods/search/save-rule
client.save_rule(ALGOLIA_INDEX_NAME, objectID, rule)

# Or save the Rule, and forward it to all replicas of the index.
response = client.save_rule(ALGOLIA_INDEX_NAME, objectID, rule, true)

# Wait for asynchronous task to complete
# https://www.algolia.com/doc/libraries/sdk/methods/search/wait-for-task
client.wait_for_task(ALGOLIA_INDEX_NAME, response.task_id)

# Browse rules
# https://www.algolia.com/doc/libraries/sdk/methods/search/browse-rules#ruby
res = client.browse_rules(ALGOLIA_INDEX_NAME)

# Store rules into an array
rules = res.map { |rule| rule } 

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

