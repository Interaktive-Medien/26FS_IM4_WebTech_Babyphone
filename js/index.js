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
      document.getElementById("emptyState").style.display = "";
      renderChart([]);
      return;
    }

    document.getElementById("emptyState").style.display = "none";

    history.forEach((entry) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${formatDateTime(entry.starttime)}</td>
        <td>${formatDateTime(entry.endtime)}</td>
        <td>${formatDurationMinutes(entry.starttime, entry.endtime)}</td>
      `;
      tbody.appendChild(row);
    });

    renderChart(history);
  } catch (error) {
    console.error("Error loading heulhistory:", error);
  }
}

function renderChart(history) {
  // Aggregate total crying minutes per day
  const minutesPerDay = {};

  history.forEach((entry) => {
    const date = new Date(entry.starttime);
    const dayKey = `${date.getDate()}.${date.getMonth() + 1}.${String(
      date.getFullYear()
    ).slice(2)}`;
    const mins = formatDurationMinutes(entry.starttime, entry.endtime);
    minutesPerDay[dayKey] = (minutesPerDay[dayKey] || 0) + mins;
  });

  // Sort by date (oldest first)
  const sorted = Object.entries(minutesPerDay).sort((a, b) => {
    const [dA, mA, yA] = a[0].split(".").map(Number);
    const [dB, mB, yB] = b[0].split(".").map(Number);
    return yA - yB || mA - mB || dA - dB;
  });

  // When empty, show placeholder day labels so axes are still visible
  const labels = sorted.length > 0
    ? sorted.map((e) => e[0])
    : ["Heute"];
  const data = sorted.length > 0
    ? sorted.map((e) => e[1])
    : [0];

  const ctx = document.getElementById("heulchart").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [
        {
          label: "Heulzeit (min)",
          data: data,
          backgroundColor: "#ff6724",
          borderRadius: 6,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
      },
      scales: {
        y: {
          beginAtZero: true,
          suggestedMax: 20,
          title: { display: true, text: "Minuten" },
        },
        x: {
          title: { display: true, text: "Tag" },
        },
      },
    },
  });
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

    // Reload the page to show the new data
    window.location.reload();
  } catch (error) {
    console.error("Seed failed:", error);
    alert("Fehler beim Erstellen der Demo-Daten");
    btn.disabled = false;
    btn.textContent = "Demo-Daten erstellen";
  }
}

// Load history when page loads
document.addEventListener("DOMContentLoaded", loadHeulhistory);
