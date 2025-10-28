---
argument-hint: <models-files> <tech-stack> <route-specification>
description: Plan and implement a REST API endpoint using specialized agents
---

You will orchestrate the complete process of planning and implementing a REST API endpoint by coordinating specialized agents.

## Parameter Validation

**Use the `parameter-validation` skill to validate the following required parameters:**

Required parameters:
- $1: `<models-files>` - Database models/entities that define the data structure for this endpoint
- $2: `<tech-stack>` - Technology stack details including framework versions, libraries, and architectural patterns
- $3: `<route-specification>` - Complete route specification including HTTP method, path, request/response format, and business logic

**STOP execution and wait for user input if any parameters are missing.**

Only proceed to Phase 1 after all 3 parameters are validated and available.

---

## Provided Inputs

Once validated, the following inputs will be used:

1. Database Models:
<application_models>
$1
</application_models>

2. Tech stack:
<tech_stack>
$2
</tech_stack>

3. Route API specification:
<route_api_specification>
$3
</route_api_specification>

Your task is to complete this workflow in three sequential phases:

## Phase 1: Planning (Software Architect Agent)

Use the Task tool with subagent_type="software-architect" to execute the planning phase. Pass the following prompt to the agent:

```
Create a detailed implementation plan for a REST API endpoint using the /plan-api-endpoint command.

Database Models: $1
Tech stack: $2
Route specification: $3

Execute: /plan-api-endpoint @($1) @($2) @($3)

Return the full file path of the saved implementation plan.
```

Wait for the software-architect agent to complete and return the implementation plan file path.

Once the file is saved, let user review the file and wait for the confirmation to continue with Phase 2.

## Phase 2: Request DTO Generation Check

After receiving the implementation plan file path from Phase 1, check if the Request DTO class exists for the endpoint being built.

1. Read the implementation plan to identify:
   - The endpoint route and HTTP method
   - The expected Request DTO class name and location

2. Check if the Request DTO class file exists at the expected location in the backend

3. If the Request DTO does NOT exist:
   - Use the Task tool with subagent_type="symfony-implementation" to generate it
   - Pass the following prompt to the agent:

```
Generate the Request DTO for the endpoint being implemented.

Database Models: $1
Implementation plan: [FILE_PATH_FROM_PHASE_1]

Execute: /generate-requests @($1) @[FILE_PATH_FROM_PHASE_1]

Ensure the Request DTO is created with proper validation rules and type hints.
```

4. If the Request DTO already exists, proceed directly to Phase 3

Wait for the Request DTO generation to complete (if needed) before proceeding to Phase 3.

## Phase 3: Implementation (Symfony Implementation Agent)

After confirming the Request DTO exists (either found or generated in Phase 2), use the Task tool with subagent_type="symfony-implementation" to execute the implementation phase. Pass the following prompt to the agent:

```
Implement the REST API endpoint following the implementation plan at: [FILE_PATH_FROM_PHASE_1]

Execute: /implement-api-endpoint @[FILE_PATH_FROM_PHASE_1]

Follow the 3x3 workflow:
- Implement maximum 3 steps from the plan
- Summarize what was done
- Describe the plan for the next 3 actions
- Wait for feedback before continuing
```

## Important Notes:

1. You must run these phases sequentially, not in parallel
2. Wait for the software-architect agent to complete before checking for Request DTOs
3. Check for Request DTO existence before starting implementation
4. Generate Request DTOs only if they don't already exist
5. Pass the actual implementation plan file path from Phase 1 to subsequent phases
6. Report progress to the user after each phase completes
7. If any agent encounters errors, stop and report to the user before proceeding

After all phases complete, provide a summary of:
- The implementation plan location
- Whether Request DTOs were generated or already existed
- What was implemented in the first 3 steps
- What's planned for the next steps
