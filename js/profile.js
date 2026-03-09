// Check if user is logged in (reusing the same function we had before)
async function checkAuth() {
  try {
    const response = await fetch("/api/auth/auth.php", {
      credentials: "include",
    });

    if (response.status === 401) {
      window.location.href = "/login.html";
      return false;
    }

    const result = await response.json();
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

// Format date function
function formatDate(dateString) {
  const date = new Date(dateString);
  return `${date.getDate()}.${date.getMonth() + 1}.${String(
    date.getFullYear()
  ).slice(2)}
            ${String(date.getHours()).padStart(2, "0")}:${String(
    date.getMinutes()
  ).padStart(2, "0")}`;
}

// Load profile data
async function loadProfile() {
  const isAuthorized = await checkAuth();
  if (!isAuthorized) return;

  try {
    const response = await fetch("/api/profile/read.php");
    const data = await response.json();

    if (data.error) {
      console.error("Error loading profile:", data.error);
      return;
    }

    // Update user info
    document.getElementById("userName").value = data.user.name;
    document.getElementById("totalScore").textContent = data.user.total_score;

    // Update activities
    const container = document.getElementById("activitiesContainer");
    container.innerHTML = "";

    data.activities.forEach((activity) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${formatDate(activity.timestamp)}</td>
        <td>${activity.emoji} ${activity.name}</td>
        <td class="score">+${activity.score}</td>
      `;
      container.appendChild(row);
    });
  } catch (error) {
    console.error("Error loading profile:", error);
  }
}

// Reuse logout function
async function logout() {
  try {
    await fetch("/api/auth/logout.php");
    window.location.href = "/login.html";
  } catch (error) {
    console.error("Logout failed:", error);
    alert("Logout failed");
  }
}

// Update user name
async function updateName() {
  const nameInput = document.getElementById("userName");
  const newName = nameInput.value.trim();

  if (!newName) {
    alert("Name cannot be empty");
    return;
  }

  try {
    const response = await fetch("/api/profile/update.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ name: newName }),
    });

    const result = await response.json();

    if (result.error) {
      alert(result.error);
      return;
    }

    alert("Name updated successfully!");
  } catch (error) {
    console.error("Error updating name:", error);
    alert("Failed to update name");
  }
}

// Add event listener for name input
document.getElementById("userName").addEventListener("change", updateName);

// Load profile when page loads
document.addEventListener("DOMContentLoaded", loadProfile);
