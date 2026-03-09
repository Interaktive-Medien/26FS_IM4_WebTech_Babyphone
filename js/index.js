// First check if user is authorized
async function checkAuth() {
  try {
    const response = await fetch("/api/auth/auth.php", {
      credentials: "include",
    });
    // ^ IMPORTANT if you need cookies

    // If server returns 401:
    if (response.status === 401) {
      window.location.href = "/login.html";
      return false;
    }

    // Otherwise parse the JSON
    const result = await response.json();

    // Possibly check if result has an error:
    if (result.error || !result.email) {
      window.location.href = "/login.html";
      return false;
    }
    return true;
  } catch (error) {
    console.error("Auth check failed:", error);
    window.location.href = "/login.html";
    return false;
  }
}

// Function to load and display all tasks
async function loadTasks() {
  // First check authorization
  const isAuthorized = await checkAuth();
  if (!isAuthorized) return;

  try {
    const response = await fetch("/api/tasks/read.php");
    const tasks = await response.json();

    if (!tasks || tasks.error) {
      console.error("Error loading tasks:", tasks.error);
      return;
    }

    const container = document.getElementById("tasks-container");
    container.innerHTML = ""; // Clear existing tasks

    // Group tasks by category
    const tasksByCategory = tasks.reduce((acc, task) => {
      if (!acc[task.category]) {
        acc[task.category] = [];
      }
      acc[task.category].push(task);
      return acc;
    }, {});

    // Create sections for each category
    Object.entries(tasksByCategory).forEach(([category, categoryTasks]) => {
      // Create category section
      const categorySection = document.createElement("div");
      categorySection.className = "category-section";

      // Add category header
      const categoryHeader = document.createElement("h2");
      categoryHeader.className = "category-header";
      categoryHeader.textContent = category;
      categorySection.appendChild(categoryHeader);

      // Add tasks for this category
      const tasksContainer = document.createElement("div");
      tasksContainer.className = "tasks-grid";

      categoryTasks.forEach((task) => {
        const button = document.createElement("button");
        button.className = "task-button";
        button.innerHTML = `
          <span class="emoji">${task.emoji}</span>
          <span class="task-name">${task.name}</span>
          <span class="task-score">${task.score}</span>
        `;
        button.onclick = () => completeTask(task.id);
        tasksContainer.appendChild(button);
      });

      categorySection.appendChild(tasksContainer);
      container.appendChild(categorySection);
    });
  } catch (error) {
    console.error("Error loading tasks:", error);
  }
}

// Function to complete a task
async function completeTask(taskId) {
  try {
    const response = await fetch("/api/tasks/complete.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        task_id: taskId,
      }),
    });

    const result = await response.json();

    if (result.error) {
      alert(result.error);
      return;
    }

    if (result.message) {
      // Fire confetti
      confetti({
        particleCount: 100,
        spread: 70,
        origin: { y: 0.6 },
      });
      loadTasks();
    }
  } catch (error) {
    console.error("Error completing task:", error);
    alert("Error completing task. Please try again.");
  }
}

// Load tasks when page loads
document.addEventListener("DOMContentLoaded", loadTasks);
