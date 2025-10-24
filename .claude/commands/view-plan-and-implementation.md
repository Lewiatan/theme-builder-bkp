---
description: "Plan and implement a frontend view using specialized agents (project)"
argument-hint: "<view-description> <user-stories> <endpoint-description> <endpoint-implementation>"
---

# View Plan and Implementation Orchestration

Execute a two-phase workflow for planning and implementing a frontend view using specialized agents.

**Required Input Parameters:**
- $1: View Description
- $2: User Stories
- $3: Endpoint Description
- $4: Endpoint Implementation

## Parameter Validation

**FIRST, validate that all required parameters have been provided:**

1. Check if all 4 parameters ($1, $2, $3, $4) are present and non-empty
2. Identify which parameters are missing or empty

**If ANY parameter is missing or empty:**

1. **STOP EXECUTION immediately** - Do not proceed to Phase 1
2. Inform the user which parameters are missing by their argument-hint names:
   - $1 = `<view-description>`
   - $2 = `<user-stories>`
   - $3 = `<endpoint-description>`
   - $4 = `<endpoint-implementation>`

3. **Request the user to provide the missing information directly** - Do NOT suggest answers or provide options

Display missing parameters clearly:
```
Missing required parameters:
- ❌ <user-stories>
- ❌ <endpoint-implementation>

Please provide the missing information to proceed with the view plan and implementation.
```

4. **Ask the user directly** for each missing parameter:
   - For missing `<view-description>`: "Please provide the `<view-description>`: Describe the view's purpose, main features, and what it should display."
   - For missing `<user-stories>`: "Please provide the `<user-stories>`: Include the relevant user stories with acceptance criteria."
   - For missing `<endpoint-description>`: "Please provide the `<endpoint-description>`: Describe the API endpoint(s) including HTTP methods, paths, and data structure."
   - For missing `<endpoint-implementation>`: "Please provide the `<endpoint-implementation>`: Detail the backend implementation including controllers, services, and response format."

5. **Wait for user response** - Do not proceed until all parameters are provided

**Important:**
- Reference parameters by their argument-hint names (e.g., `<view-description>`, not "view description")
- Do NOT use AskUserQuestion tool with predefined options
- Simply ask for text input in your response to the user
- After user provides missing information, validate again before proceeding

**Only proceed to Phase 1 after ALL 4 parameters are validated and available (either from original $1-$4 or provided by user).**

---

## Instructions

### Phase 1: Planning Phase

Use the Task tool to launch a **software-architect** agent with the following task:

**Task Description:** "Create view implementation plan"

**Task Prompt:**
```
Execute the /view-plan command with the following parameters:

View Description: [Use validated $1 or collected parameter]

User Stories: [Use validated $2 or collected parameter]

Endpoint Description: [Use validated $3 or collected parameter]

Endpoint Implementation: [Use validated $4 or collected parameter]

Create a comprehensive implementation plan following the view-plan command structure. The plan should:
1. Analyze the PRD (@.ai/prd.md) and tech stack (@.ai/tech-stack.md)
2. Conduct thorough implementation breakdown in thinking block
3. Generate detailed implementation plan with all required sections
4. Save to .ai/views/{view-name}-view-implementation-plan.md

Return the file path of the generated implementation plan in your final report.
```

**After the software-architect agent completes:**

1. Display the path to the generated implementation plan
2. Ask the user to review the plan
3. **STOP EXECUTION HERE** and explicitly ask for confirmation with these options:
   - ✅ Type "proceed" or "continue" to start implementation
   - ✏️ Type "modify" followed by requested changes
   - ❌ Type "stop" to end the workflow

**DO NOT PROCEED TO PHASE 2 WITHOUT EXPLICIT USER CONFIRMATION.**

---

### Phase 2: Implementation Phase

**ONLY execute this phase after receiving explicit user confirmation to proceed.**

Use the Task tool to launch a **react-implementation** agent with the following task:

**Task Description:** "Implement view from plan"

**Task Prompt:**
```
Execute the /view-implementation command using the implementation plan at:

@.ai/views/{view-name}-view-implementation-plan.md

Implement the view following the 3x3 workflow:
1. Implement maximum 3 steps from the plan
2. Summarize what was done
3. Describe the next 3 planned actions
4. Stop and wait for feedback

Create all necessary files including:
- React components with TypeScript
- Type definitions and interfaces
- Custom hooks if needed
- API integration functions
- State management
- Styling with Tailwind CSS

Follow the implementation plan precisely and adhere to all specified requirements.
```

---

## Execution Flow Summary

1. **Validate Parameters** - Check all 4 required parameters are provided
   - If missing: Display which `<parameter-names>` are missing and ask user to provide them
   - Wait for user response and re-validate
   - If complete: Proceed to next step
2. Launch software-architect agent for planning
3. Wait for planning completion
4. **MANDATORY STOP** - Request user confirmation
5. Only after confirmation: Launch react-implementation agent
6. Follow 3x3 implementation workflow with feedback checkpoints

---

## Important Notes

- **Parameter Naming**: Always refer to parameters using their argument-hint names: `<view-description>`, `<user-stories>`, `<endpoint-description>`, `<endpoint-implementation>`
- **Direct Requests**: Ask user to provide missing parameters directly via text response, do NOT use AskUserQuestion tool or provide multiple choice options
- **Parameter Collection**: If multiple parameters are missing, list them all at once and wait for user to provide them
- **Parameter Usage**: Use the validated/collected parameters when constructing the task prompts for both agents
- **No Assumptions**: Never proceed with empty or placeholder parameters - always wait for user input
- **Validation Loop**: Re-validate after user provides information before proceeding to Phase 1
