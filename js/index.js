// First check if user is authorized
async function checkAuth() {
  try {
    const response = await fetch("api/auth/auth.php", {
      credentials: "include",
    });
    // ^ IMPORTANT if you need cookies

    // If server returns 401:
    if (response.status === 401) {
      window.location.href = "login.html";
      return false;
    }

    // Otherwise parse the JSON
    const result = await response.json();

    // Possibly check if result has an error:
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

function formatDateTime(dateString) {
  const date = new Date(dateString);
  return `${date.getDate()}.${date.getMonth() + 1}.${String(
    date.getFullYear()
  ).slice(2)} ${String(date.getHours()).padStart(2, "0")}:${String(
    date.getMinutes()
  ).padStart(2, "0")}`;
}

function formatDurationMinutes(startTime, endTime) {
  const start = new Date(startTime).getTime();
  const end = new Date(endTime).getTime();
  const minutes = Math.max(0, Math.round((end - start) / 60000));
  return minutes;
}

// Function to load and display all crying events
async function loadHeulhistory() {
  // First check authorization
  const isAuthorized = await checkAuth();
  if (!isAuthorized) return;

  try {
    const response = await fetch("api/heulhistory/read.php");
    const history = await response.json();

    if (!history || history.error) {
      console.error("Error loading heulhistory:", history.error);
      return;
    }

    const tbody = document.getElementById("heulhistory-body");
    tbody.innerHTML = ""; // Clear existing rows

    if (history.length === 0) {
      const row = document.createElement("tr");
      row.innerHTML = '<td colspan="3">Keine Eintraege vorhanden.</td>';
      tbody.appendChild(row);
      return;
    }

    history.forEach((entry) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${formatDateTime(entry.starttime)}</td>
        <td>${formatDateTime(entry.endtime)}</td>
        <td>${formatDurationMinutes(entry.starttime, entry.endtime)}</td>
      `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Error loading heulhistory:", error);
  }
}

// Load history when page loads
document.addEventListener("DOMContentLoaded", loadHeulhistory);
