(() => {
    "use strict";

    const root = document.getElementById("complaints-app");
    if (!root) {
        return;
    }

    const storageKey = "gocomplain.complaints.v1";
    const page = root.dataset.complaintsPage;
    const userId = root.dataset.userId;
    const isAdmin = root.dataset.isAdmin === "true";
    const errorBox = document.getElementById("complaints-error");
    const statuses = ["New", "In Progress", "Resolved", "Rejected"];
    const priorities = ["Low", "Medium", "High", "Urgent"];

    function showError(error) {
        console.error("Complaint storage error:", error);
        errorBox.textContent = error.message || "Unable to access saved complaints.";
        errorBox.hidden = false;
    }

    function run(action) {
        try {
            action();
        } catch (error) {
            showError(error);
        }
    }

    function readComplaints() {
        let value;
        try {
            value = localStorage.getItem(storageKey);
        } catch (error) {
            throw new Error("Browser storage is unavailable. Enable local storage and try again.");
        }

        if (value === null) {
            return [];
        }

        let complaints;
        try {
            complaints = JSON.parse(value);
        } catch (error) {
            throw new Error("Saved complaints could not be read because the stored data is invalid.");
        }

        if (!Array.isArray(complaints) || complaints.some((complaint) =>
            !complaint || typeof complaint !== "object" ||
            typeof complaint.id !== "string" ||
            typeof complaint.user_id !== "string" ||
            typeof complaint.complaint_code !== "string"
        )) {
            throw new Error("Saved complaints have an invalid format. The data was not changed.");
        }

        return complaints;
    }

    function saveComplaints(complaints) {
        try {
            localStorage.setItem(storageKey, JSON.stringify(complaints));
        } catch (error) {
            throw new Error("Could not save complaints to browser storage. Check available storage space and try again.");
        }
    }

    function visibleComplaints() {
        const complaints = readComplaints();
        return (isAdmin ? complaints : complaints.filter((item) => item.user_id === userId))
            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }

    function escapeHtml(value) {
        return String(value ?? "").replace(/[&<>"']/g, (character) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        })[character]);
    }

    function formatDate(value) {
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? "—" : date.toLocaleString();
    }

    function emptyRow(columns, message) {
        return `<tr><td colspan="${columns}">${escapeHtml(message)}</td></tr>`;
    }

    function complaintId() {
        return typeof crypto !== "undefined" && typeof crypto.randomUUID === "function"
            ? crypto.randomUUID()
            : `${Date.now()}-${Math.random().toString(36).slice(2)}`;
    }

    function complaintCode() {
        return `CMP-${Date.now().toString(36).toUpperCase()}-${Math.random().toString(36).slice(2, 7).toUpperCase()}`;
    }

    function renderDashboard() {
        const complaints = visibleComplaints();
        const count = (status) => complaints.filter((item) => item.status === status).length;
        document.getElementById("complaint-total").textContent = complaints.length;
        document.getElementById("complaint-pending").textContent = count("New");
        document.getElementById("complaint-progress").textContent = count("In Progress");
        document.getElementById("complaint-resolved").textContent = count("Resolved");

        const columns = isAdmin ? 6 : 5;
        document.getElementById("dashboard-complaints").innerHTML = complaints.length
            ? complaints.slice(0, 6).map((item) => `
                <tr>
                    <td>${escapeHtml(item.complaint_code)}</td>
                    ${isAdmin ? `<td>${escapeHtml(item.user_name)}</td>` : ""}
                    <td>${escapeHtml(item.category)}</td>
                    <td>${escapeHtml(item.subject)}</td>
                    <td><span class="status">${escapeHtml(item.status)}</span></td>
                    <td>${escapeHtml(formatDate(item.created_at))}</td>
                </tr>
            `).join("")
            : emptyRow(columns, "No complaints found.");
    }

    function renderMyComplaints() {
        const complaints = visibleComplaints();
        document.getElementById("my-complaints").innerHTML = complaints.length
            ? complaints.map((item) => `
                <tr>
                    <td>${escapeHtml(item.complaint_code)}</td>
                    <td>${escapeHtml(item.category)}</td>
                    <td>${escapeHtml(item.subject)}</td>
                    <td>${escapeHtml(item.status)}</td>
                    <td>${escapeHtml(item.priority)}</td>
                    <td>${escapeHtml(formatDate(item.created_at))}</td>
                    <td><a class="text-link" href="track_complaints.php?id=${encodeURIComponent(item.id)}">Track</a></td>
                </tr>
            `).join("")
            : emptyRow(7, "No complaints submitted yet.");
    }

    function renderManagedComplaints() {
        const complaints = visibleComplaints();
        const body = document.getElementById("managed-complaints");
        body.innerHTML = complaints.length
            ? complaints.map((item) => `
                <tr>
                    <td>${escapeHtml(item.complaint_code)}</td>
                    <td>${escapeHtml(item.user_name)}</td>
                    <td>${escapeHtml(item.category)}</td>
                    <td>${escapeHtml(item.subject)}</td>
                    <td>${escapeHtml(item.status)}</td>
                    <td>${escapeHtml(item.priority)}</td>
                    <td>
                        <a class="text-link" href="edit_complaints.php?id=${encodeURIComponent(item.id)}">Edit</a>
                        <button class="link-danger" type="button" data-delete-id="${escapeHtml(item.id)}">Delete</button>
                    </td>
                </tr>
            `).join("")
            : emptyRow(7, "No complaints available.");

        body.onclick = (event) => {
            const button = event.target.closest("[data-delete-id]");
            if (!button || !window.confirm("Delete this complaint?")) {
                return;
            }

            run(() => {
                saveComplaints(readComplaints().filter((item) => item.id !== button.dataset.deleteId));
                renderManagedComplaints();
            });
        };
    }

    function initializeSubmitForm() {
        document.getElementById("submit-complaint-form").addEventListener("submit", (event) => {
            event.preventDefault();
            run(() => {
                const form = event.currentTarget;
                const values = new FormData(form);
                const now = new Date().toISOString();
                const complaint = {
                    id: complaintId(),
                    complaint_code: complaintCode(),
                    user_id: userId,
                    user_name: root.dataset.userName,
                    category: String(values.get("category")).trim(),
                    subject: String(values.get("subject")).trim(),
                    description: String(values.get("description")).trim(),
                    address: String(values.get("address")).trim(),
                    status: "New",
                    priority: "Medium",
                    admin_remarks: "",
                    created_at: now,
                    updated_at: now
                };

                if (!complaint.category || !complaint.subject || !complaint.description || !complaint.address) {
                    throw new Error("Please complete all fields.");
                }

                const complaints = readComplaints();
                complaints.push(complaint);
                saveComplaints(complaints);
                window.location.assign("my_complaints.php");
            });
        });
    }

    function initializeEditForm() {
        const id = root.dataset.complaintId;
        const complaint = readComplaints().find((item) => item.id === id);
        if (!complaint) {
            throw new Error("Complaint not found in this browser's local storage.");
        }

        document.getElementById("edit-complaint-details").innerHTML = `
            <p><b>Complaint ID:</b> ${escapeHtml(complaint.complaint_code)}</p>
            <p><b>Subject:</b> ${escapeHtml(complaint.subject)}</p>
            <p><b>Description:</b><br>${escapeHtml(complaint.description).replace(/\n/g, "<br>")}</p>
        `;

        const form = document.getElementById("edit-complaint-form");
        form.elements.status.value = complaint.status;
        form.elements.priority.value = complaint.priority;
        form.elements.admin_remarks.value = complaint.admin_remarks || "";
        form.addEventListener("submit", (event) => {
            event.preventDefault();
            run(() => {
                const status = form.elements.status.value;
                const priority = form.elements.priority.value;
                if (!statuses.includes(status) || !priorities.includes(priority)) {
                    throw new Error("Choose a valid status and priority.");
                }

                const complaints = readComplaints();
                const index = complaints.findIndex((item) => item.id === id);
                if (index < 0) {
                    throw new Error("Complaint no longer exists in browser storage.");
                }

                complaints[index] = {
                    ...complaints[index],
                    status,
                    priority,
                    admin_remarks: form.elements.admin_remarks.value.trim(),
                    updated_at: new Date().toISOString()
                };
                saveComplaints(complaints);
                window.location.assign("manage_complaints.php");
            });
        });
    }

    function renderTracking(complaint) {
        document.getElementById("tracking-result").innerHTML = `
            <div class="tracking-card">
                <h2>${escapeHtml(complaint.complaint_code)}</h2>
                <p><b>Subject:</b> ${escapeHtml(complaint.subject)}</p>
                <p><b>Category:</b> ${escapeHtml(complaint.category)}</p>
                <p><b>Status:</b> ${escapeHtml(complaint.status)}</p>
                <p><b>Priority:</b> ${escapeHtml(complaint.priority)}</p>
                <p><b>Date Submitted:</b> ${escapeHtml(formatDate(complaint.created_at))}</p>
                <p><b>Last Updated:</b> ${escapeHtml(formatDate(complaint.updated_at))}</p>
                <p><b>Description:</b><br>${escapeHtml(complaint.description).replace(/\n/g, "<br>")}</p>
                <p><b>Admin Remarks:</b><br>${escapeHtml(complaint.admin_remarks || "No updates yet.").replace(/\n/g, "<br>")}</p>
            </div>
        `;
    }

    function initializeTracking() {
        const complaints = visibleComplaints();
        const form = document.getElementById("track-complaint-form");
        const id = root.dataset.complaintId;

        if (id) {
            const match = complaints.find((item) => item.id === id);
            if (match) {
                form.elements.complaint_code.value = match.complaint_code;
                renderTracking(match);
            }
        }

        form.addEventListener("submit", (event) => {
            event.preventDefault();
            run(() => {
                const code = form.elements.complaint_code.value.trim().toLowerCase();
                const match = visibleComplaints().find((item) =>
                    item.complaint_code.toLowerCase() === code
                );
                const result = document.getElementById("tracking-result");
                if (!match) {
                    result.innerHTML = '<div class="alert error">Complaint not found.</div>';
                    return;
                }
                renderTracking(match);
            });
        });
    }

    function renderReports() {
        const complaints = visibleComplaints();
        const count = (status) => complaints.filter((item) => item.status === status).length;
        document.getElementById("report-total").textContent = complaints.length;
        document.getElementById("report-new").textContent = count("New");
        document.getElementById("report-progress").textContent = count("In Progress");
        document.getElementById("report-resolved").textContent = count("Resolved");

        document.getElementById("report-statuses").innerHTML = statuses.map((status) => {
            const total = count(status);
            const percentage = complaints.length ? (total / complaints.length) * 100 : 0;
            return `
                <div class="bar-row">
                    <div><span>${escapeHtml(status)}</span><b>${total}</b></div>
                    <div class="bar-track"><i style="width:${percentage}%"></i></div>
                </div>
            `;
        }).join("");

        const categories = new Map();
        complaints.forEach((item) => categories.set(item.category, (categories.get(item.category) || 0) + 1));
        document.getElementById("report-categories").innerHTML = categories.size
            ? Array.from(categories.entries()).sort((a, b) => b[1] - a[1]).map(([category, total]) => {
                const percentage = complaints.length ? (total / complaints.length) * 100 : 0;
                return `
                    <div class="bar-row">
                        <div><span>${escapeHtml(category)}</span><b>${total}</b></div>
                        <div class="bar-track"><i style="width:${percentage}%"></i></div>
                    </div>
                `;
            }).join("")
            : '<p class="muted">No complaint data available.</p>';

        const monthly = new Map();
        const cutoff = new Date();
        cutoff.setDate(1);
        cutoff.setMonth(cutoff.getMonth() - 5);
        complaints.forEach((item) => {
            const date = new Date(item.created_at);
            if (Number.isNaN(date.getTime()) || date < cutoff) {
                return;
            }
            const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
            monthly.set(key, (monthly.get(key) || 0) + 1);
        });
        const months = Array.from(monthly.entries()).sort((a, b) => a[0].localeCompare(b[0]));
        document.getElementById("report-monthly").innerHTML = months.length
            ? months.map(([key, total]) => {
                const [year, month] = key.split("-");
                const label = new Date(Number(year), Number(month) - 1, 1)
                    .toLocaleDateString(undefined, { month: "long", year: "numeric" });
                return `<tr><td>${escapeHtml(label)}</td><td>${total}</td></tr>`;
            }).join("")
            : emptyRow(2, "No complaint data available.");
    }

    run(() => {
        switch (page) {
            case "dashboard":
                renderDashboard();
                break;
            case "my-complaints":
                renderMyComplaints();
                break;
            case "manage-complaints":
                renderManagedComplaints();
                break;
            case "submit-complaint":
                initializeSubmitForm();
                break;
            case "edit-complaint":
                initializeEditForm();
                break;
            case "track-complaint":
                initializeTracking();
                break;
            case "reports":
                renderReports();
                break;
            default:
                throw new Error("Unknown complaint page.");
        }
    });
})();
