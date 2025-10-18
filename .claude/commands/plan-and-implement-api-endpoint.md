---
argument-hint: <db-resources> <tech-stack> <route-specification>
description: Plan and implement a REST API endpoint using specialized agents
---

You will orchestrate the complete process of planning and implementing a REST API endpoint by coordinating two specialized agents.

First, review the provided inputs:

1. Related database resources:
<related_db_resources>
$1
</related_db_resources>

2. Tech stack:
<tech_stack>
$2
</tech_stack>

3. Route API specification:
<route_api_specification>
$3
</route_api_specification>

Your task is to complete this workflow in two sequential phases:

## Phase 1: Planning (Software Architect Agent)

Use the Task tool with subagent_type="software-architect" to execute the planning phase. Pass the following prompt to the agent:

```
Create a detailed implementation plan for a REST API endpoint using the /plan-api-endpoint command.

Database resources: $1
Tech stack: $2
Route specification: $3

Execute: /plan-api-endpoint @($1) @($2) @($3)

Return the full file path of the saved implementation plan.
```

Wait for the software-architect agent to complete and return the implementation plan file path.

## Phase 2: Implementation (Symfony Implementation Agent)

After receiving the implementation plan file path from Phase 1, use the Task tool with subagent_type="symfony-implementation" to execute the implementation phase. Pass the following prompt to the agent:

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

1. You must run these agents sequentially, not in parallel
2. Wait for the software-architect agent to complete before starting the symfony-implementation agent
3. Pass the actual implementation plan file path from Phase 1 to Phase 2
4. Report progress to the user after each phase completes
5. If either agent encounters errors, stop and report to the user before proceeding

After both phases complete, provide a summary of:
- The implementation plan location
- What was implemented in the first 3 steps
- What's planned for the next steps
