// Check if user is logged in (reusing the same function we had before)
async function checkAuth() {
  try {
    const response = await fetch("api/auth/auth.php", {
      credentials: "include",
    });

    if (response.status === 401) {
      window.location.href = "login.html";
      return false;
    }

    const result = await response.json();
    if (result.error || !result.email) {
      window.location.href = "login.html";
      return false;
    }
    return true;
  } catch (error) {
    console.error("Auth check failed:", error);
    window.location.href = "login.html";
    return false;
  }
}

// Load profile data
async function loadProfile() {
  const isAuthorized = await checkAuth();
  if (!isAuthorized) return;

  try {
    const response = await fetch("api/profile/read.php");
    const data = await response.json();

    if (data.error) {
      console.error("Error loading profile:", data.error);
      return;
    }

    // Update user info
    document.getElementById("userName").value = data.user.name;

  } catch (error) {
    console.error("Error loading profile:", error);
  }
}

// Reuse logout function
async function logout() {
  try {
    await fetch("api/auth/logout.php");
    window.location.href = "login.html";
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
    const response = await fetch("api/profile/update.php", {
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

// Check if user has heulhistory entries; show seed button if not
async function checkHeulhistory() {
  try {
    const response = await fetch("api/heulhistory/read.php", {
      credentials: "include",
    });
    const data = await response.json();

    if (Array.isArray(data) && data.length === 0) {
      document.getElementById("seedSection").style.display = "";
    }
  } catch (error) {
    console.error("Error checking heulhistory:", error);
  }
}

// Seed demo heulhistory entries for the current user
async function seedDatabase() {
  const btn = document.getElementById("seedBtn");
  btn.disabled = true;
  btn.textContent = "Wird erstellt…";

  try {
    const response = await fetch("api/heulhistory/seed.php", {
      method: "POST",
      credentials: "include",
    });
    const result = await response.json();

    if (result.error) {
      alert(result.error);
      btn.disabled = false;
      btn.textContent = "Demo-Daten erstellen";
      return;
    }

    document.getElementById("seedSection").style.display = "none";
    alert("Demo-Daten wurden erfolgreich erstellt!");
  } catch (error) {
    console.error("Seed failed:", error);
    alert("Fehler beim Erstellen der Demo-Daten");
    btn.disabled = false;
    btn.textContent = "Demo-Daten erstellen";
  }
}

// Add event listener for name input
document.getElementById("userName").addEventListener("change", updateName);

// Load profile when page loads
document.addEventListener("DOMContentLoaded", () => {
  loadProfile();
  checkHeulhistory();
});
