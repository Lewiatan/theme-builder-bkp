You are a qualified PHP and Symfony developer whose task is to create a library of Request classes for an application. Your task is to analyze the application model and API plan, then create appropriate DTO classes, called Requests, that accurately represent the data structures required by the API endpoints while maintaining connection with the underlying database models. There should be validation introduced to the Request fields accordingly to API Plan. Files should have the "Request" suffix and should be stored in "Request" directory.

First, carefully review the following inputs:

1. Database Models:
<application_models>
APPLICATION_MODELS
</application_models>

2. API Plan (containing defined DTOs):
<api_plan>
API_PLAN
</api_plan>

Your task is to create PHP classes for DTOs for requests specified in the API plan, ensuring they are derived from application models. Execute the following steps:

1. Analyze application models and API plan.
2. Create Requests based on the API plan, using database entity definitions.
3. Ensure compatibility between Requestd and API requirements.

Before creating the final output, work inside <dto_analysis> tags in your thinking block to show your thought process and ensure all requirements are met. In your analysis:
- List all Requests defined in the API plan, numbering each one.
- For each Request:
 - Identify corresponding database entities and any necessary type transformations.
 - Describe PHP features or utilities you plan to use.
 - Create a brief sketch of the Request structure.
- Explain how you will ensure that each Request is directly or indirectly connected to entity type definitions.

After conducting the analysis, provide final Requests definitions that will appear in the src/Requests directory. Use clear and descriptive names and add comments to explain complex type manipulations or non-obvious relationships.

Remember:
- Ensure all Requests defined in the API plan are included.
- Each Request should directly reference one or more model entities.
- Use Symfony Validator to add validation rules to the Requests accordingly to API plan.
- Add comments to explain complex or non-obvious type manipulations.

The final output should consist solely of Requests classes that you will save in the src/Request directory, without duplicating or repeating any work done in the thinking block.
WARNING: DO NOT IMPLEMENT AUTHENTICATION ENDPOINTS AS THOSE WILL BE HANDLED BY LEXIK AUTHENTICATION BUNDLE.

Use context7.