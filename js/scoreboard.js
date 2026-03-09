// First check if user is authorized
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

// Function to load and display scoreboard
async function loadScoreboard() {
  const isAuthorized = await checkAuth();
  if (!isAuthorized) return;

  try {
    const response = await fetch("/api/scoreboard/read.php");
    const scores = await response.json();

    if (!scores || scores.error) {
      console.error("Error loading scoreboard:", scores.error);
      return;
    }

    const tbody = document.getElementById("scoreboard-body");
    tbody.innerHTML = ""; // Clear existing scores

    scores.forEach((score, index) => {
      console.log("Current row:", {
        scoreEmail: score.email,
        currentUserEmail: score.currentUserEmail,
        isOwnProfile: score.email === score.currentUserEmail,
      });

      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${index + 1}</td>
        <td>${score.name}</td>
        <td>${score.total_score}</td>
        <td class="delete-column">
          ${
            score.email === score.currentUserEmail
              ? ""
              : `<button 
              onclick="deleteFriend('${score.email}', this)" 
              class="delete-btn"
            >
              Löschen
            </button>`
          }
        </td>
      `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Error loading scoreboard:", error);
  }
}

let searchTimeout;

document.getElementById("friendEmail").addEventListener("input", function (e) {
  clearTimeout(searchTimeout);
  const searchTerm = e.target.value.trim();

  if (searchTerm.length < 2) {
    document.getElementById("searchDropdown").style.display = "none";
    return;
  }

  searchTimeout = setTimeout(async () => {
    try {
      const response = await fetch(
        `/api/scoreboard/search_users.php?search=${encodeURIComponent(
          searchTerm
        )}`
      );
      const users = await response.json();

      const dropdown = document.getElementById("searchDropdown");

      if (users.length === 0) {
        dropdown.style.display = "none";
        return;
      }

      dropdown.innerHTML = users
        .map(
          (user) => `
        <div class="search-dropdown-item" onclick="addFriend('${user.email}')">
          <span class="search-dropdown-name">${user.name}</span>
          <span class="search-dropdown-email">${user.email}</span>
        </div>
      `
        )
        .join("");

      dropdown.style.display = "block";
    } catch (error) {
      console.error("Error searching users:", error);
    }
  }, 300);
});

// Close dropdown when clicking outside
document.addEventListener("click", function (e) {
  if (!e.target.closest(".friend-input-container")) {
    document.getElementById("searchDropdown").style.display = "none";
  }
});

// Update addFriend function to accept email parameter
async function addFriend(friendEmail) {
  if (!friendEmail) return;

  try {
    const response = await fetch("/api/scoreboard/add_friend.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ friendEmail }),
    });

    const result = await response.json();

    if (result.error) {
      alert(result.error);
      return;
    }

    alert("Mitglied erfolgreich hinzugefügt!");
    document.getElementById("friendEmail").value = "";
    document.getElementById("searchDropdown").style.display = "none";
    loadScoreboard();
  } catch (error) {
    console.error("Error adding friend:", error);
    alert("Failed to add friend");
  }
}

async function deleteFriend(friendEmail, buttonElement) {
  if (
    !confirm("Möchtest du dieses Mitglied wirklich aus deiner Liste entfernen?")
  ) {
    return;
  }

  try {
    const response = await fetch("/api/scoreboard/delete_friend.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ friendEmail }),
    });

    const result = await response.json();

    if (result.error) {
      alert(result.error);
      return;
    }

    // Remove the entire table row from the DOM
    buttonElement.closest("tr").remove();
  } catch (error) {
    console.error("Error deleting friend:", error);
    alert("Failed to delete friend");
  }
}

// Load scoreboard when page loads
document.addEventListener("DOMContentLoaded", loadScoreboard);
