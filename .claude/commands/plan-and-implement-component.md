---
argument-hint: <component-name>
description: Plan and implement a frontend reusable component using specialized agents
---

You will orchestrate the complete process of planning and implementing a frontend reusable component by coordinating specialized agents.

First, review the provided inputs:

1. Component being implemented:
<component_name>
$1
</component_name>

Your task is to complete this workflow in two sequential phases:

## Phase 1: Planning (Software Architect Agent)

Use the Task tool with subagent_type="software-architect" to execute the planning phase. Pass the following prompt to the agent:

```
Create a detailed implementation plan for a frontend reusable component using the /component-plan command.

Component name: $1

Execute: /component-plan @($1)

Return the full file path of the saved implementation plan.
```

Wait for the software-architect agent to complete and return the implementation plan file path.

Once the file is saved, let the user review the file and wait for the confirmation to continue with Phase 2.

## Phase 2: Implementation (React Implementation Agent)

After receiving user confirmation, READ the implementation plan file to ensure you have the latest version (the user may have modified it during review).

Use the Task tool with subagent_type="react-implementation" to execute the implementation phase. Pass the following prompt to the agent:

```
Implement the frontend reusable component following the implementation plan at: [FILE_PATH_FROM_PHASE_1]

First, read the implementation plan to get the latest version:
[READ_THE_FILE_CONTENT_HERE]

Execute: /component-implementation @[FILE_PATH_FROM_PHASE_1]

Follow the 3x3 workflow:
- Implement maximum 3 steps from the plan
- Summarize what was done
- Describe the plan for the next 3 actions
- Wait for feedback before continuing
```

## Important Notes:

1. You must run these phases sequentially, not in parallel
2. Wait for the software-architect agent to complete Phase 1 before proceeding
3. After Phase 1 completes, pause and inform the user that the plan is ready for review
4. Wait for explicit user confirmation before starting Phase 2
5. ALWAYS read the implementation plan file before Phase 2 to capture any user modifications
6. Pass the actual implementation plan file path from Phase 1 to Phase 2
7. Report progress to the user after each phase completes
8. If any agent encounters errors, stop and report to the user before proceeding

After all phases complete, provide a summary of:
- The implementation plan location
- What was implemented in the first 3 steps
- What's planned for the next steps
