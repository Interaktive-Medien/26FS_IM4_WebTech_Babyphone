// First check if user is authorized
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

async function loadTracks() {
  const isAuthorized = await checkAuth();
  if (!isAuthorized) return;

  try {
    const response = await fetch("api/tracks/read.php");
    const tracks = await response.json();

    if (!tracks || tracks.error) {
      console.error("Error loading tracks:", tracks.error);
      const tbody = document.getElementById("tracks-body");
      tbody.innerHTML = `<tr><td colspan="2">${tracks.error || "Fehler beim Laden"}</td></tr>`;
      return;
    }

    const tbody = document.getElementById("tracks-body");
    tbody.innerHTML = "";

    tracks.forEach((track) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${track.title}</td>
        <td>
          <input
            type="checkbox"
            class="delete-checkbox"
            ${Number(track.selected) === 1 ? "checked" : ""}
            onchange="updateTrackSelection(${track.id}, this.checked)"
          />
        </td>
      `;
      tbody.appendChild(row);
    });
  } catch (error) {
    console.error("Error loading tracks:", error);
  }
}

async function updateTrackSelection(trackId, selected) {
  try {
    const response = await fetch("api/tracks/update_selected.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        track_id: trackId,
        selected: selected ? 1 : 0,
      }),
    });

    const result = await response.json();
    if (result.error) {
      alert(result.error);
    }
  } catch (error) {
    console.error("Error updating track:", error);
    alert("Failed to update track");
  }
}

document.addEventListener("DOMContentLoaded", loadTracks);
