# View Plan and Implementation Orchestration

Execute a two-phase workflow for planning and implementing a frontend view using specialized agents.

**Input Parameters:**
- $1: View Description
- $2: User Stories
- $3: Endpoint Description
- $4: Endpoint Implementation

## Instructions

### Phase 1: Planning Phase

Use the Task tool to launch a **software-architect** agent with the following task:

**Task Description:** "Create view implementation plan"

**Task Prompt:**
```
Execute the /view-plan command with the following parameters:

View Description: $1

User Stories: $2

Endpoint Description: $3

Endpoint Implementation: $4

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

1. Launch software-architect agent for planning
2. Wait for planning completion
3. **MANDATORY STOP** - Request user confirmation
4. Only after confirmation: Launch react-implementation agent
5. Follow 3x3 implementation workflow with feedback checkpoints
