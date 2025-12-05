// app.js — FINAL BACKEND + POMODORO VERSION (FULL FIXED)
// ======================================================

(function () {
  const API_BASE = "/trackingprogressbelajar/project-root/public/api";
  const qs = (s) => document.querySelector(s);

  // ============================
  // ELEMENT COLLECTION
  // ============================
  const el = {
    coursesList: qs("#coursesList"),
    sessionsTableBody: qs("#sessionsTableBody"),

    // course
    formCourse: qs("#formCourse"),
    courseId: qs("#courseId"),
    courseCode: qs("#courseCode"),
    courseTitle: qs("#courseTitle"),
    courseCategory: qs("#courseCategory"),
    courseColor: qs("#courseColor"),

    // session
    formSession: qs("#formSession"),
    sessionId: qs("#sessionId"),
    sessionCourse: qs("#sessionCourse"),
    sessionStart: qs("#sessionStart"),
    sessionEnd: qs("#sessionEnd"),
    sessionEffective: qs("#sessionEffective"),
    sessionNote: qs("#sessionNote"),

    // pomodoro
    pomoDisplay: qs("#pomo-display"),
    pomoMode: qs("#pomo-mode"),
    pomoCourse: qs("#pomo-course"),
    pomoStart: qs("#pomo-start"),
    pomoPause: qs("#pomo-pause"),
    pomoReset: qs("#pomo-reset"),

    cardTotalHours: qs("#card-total-hours"),
    cardEffectiveRate: qs("#card-effective-rate"),
  };

  // ============================
  // API HELPERS
  // ============================
  async function apiGet(path) {
    const r = await fetch(API_BASE + path, { credentials: "include" });
    return r.json();
  }

  async function apiPost(path, data) {
    const r = await fetch(API_BASE + path, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data),
    });
    return r.json();
  }

  async function apiPut(path, data) {
    const r = await fetch(API_BASE + path, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data),
    });
    return r.json();
  }

  async function apiDelete(path) {
    const r = await fetch(API_BASE + path, {
      method: "DELETE",
      credentials: "include",
    });
    return r.json();
  }

  // ============================
  // RENDER COURSES
  // ============================
  async function renderCourses() {
    const res = await apiGet("/courses");
    const list = res.data || [];

    el.coursesList.innerHTML = "";
    el.sessionCourse.innerHTML = "";
    el.pomoCourse.innerHTML = "";

    list.forEach((c) => {
      const li = document.createElement("li");
      li.className =
        "list-group-item d-flex justify-content-between align-items-center";

      li.innerHTML = `
        <div>
          <div class="fw-bold">${c.title}</div>
          <div class="text-muted small">${c.code} • ${c.category}</div>
        </div>
        <div class="text-end">
          <span class="badge rounded-pill" style="background:${c.color};">&nbsp;&nbsp;</span>
          <div class="mt-2">
            <button class="btn btn-sm btn-outline-secondary me-1" 
              data-action="edit-course" data-id="${c.id}">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" 
              data-action="del-course" data-id="${c.id}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `;

      el.coursesList.appendChild(li);

      // dropdown session
      const opt = document.createElement("option");
      opt.value = c.id;
      opt.textContent = `${c.code} — ${c.title}`;
      el.sessionCourse.appendChild(opt);

      // pomodoro dropdown
      const opt2 = document.createElement("option");
      opt2.value = c.id;
      opt2.textContent = c.title;
      el.pomoCourse.appendChild(opt2);
    });
  }

  // ============================
  // RENDER SESSIONS
  // ============================
  async function renderSessions() {
    const res = await apiGet("/sessions");
    const sessions = res.data || [];

    const resCourses = await apiGet("/courses");
    const courseMap = Object.fromEntries(
      (resCourses.data || []).map((c) => [c.id, c])
    );

    el.sessionsTableBody.innerHTML = "";

    sessions.forEach((s) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `
        <td>${courseMap[s.course_id]?.title || "-"}</td>
        <td>${s.started_at}</td>
        <td>${s.ended_at}</td>
        <td>${s.effective_minutes}</td>
        <td>${s.note || ""}</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-secondary" 
            data-action="edit-session" data-id="${s.id}">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" 
            data-action="del-session" data-id="${s.id}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;

      el.sessionsTableBody.appendChild(tr);
    });
  }

  // ============================
  // SUBMIT COURSE
  // ============================
  el.formCourse.addEventListener("submit", async function (e) {
    e.preventDefault();

    const id = el.courseId.value;
    const data = {
      code: el.courseCode.value,
      title: el.courseTitle.value,
      category: el.courseCategory.value,
      color: el.courseColor.value,
    };

    if (id) {
      await apiPut(`/courses/${id}`, data);
    } else {
      await apiPost("/courses", data);
    }

    bootstrap.Modal.getInstance(
      document.getElementById("modalAddCourse")
    ).hide();

    el.formCourse.reset();
    renderCourses();
  });

  // ============================
  // COURSE ACTION BUTTONS
  // ============================
  el.coursesList.addEventListener("click", async function (e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "del-course") {
      if (confirm("Yakin hapus mata kuliah ini?")) {
        await apiDelete(`/courses/${id}`);
        renderCourses();
      }
      return;
    }

    if (action === "edit-course") {
      const res = await apiGet("/courses");
      const c = res.data.find((x) => x.id == id);

      el.courseId.value = c.id;
      el.courseCode.value = c.code;
      el.courseTitle.value = c.title;
      el.courseCategory.value = c.category;
      el.courseColor.value = c.color;

      new bootstrap.Modal(document.getElementById("modalAddCourse")).show();
    }
  });

  // ============================
  // SUBMIT SESSION
  // ============================
  el.formSession.addEventListener("submit", async function (e) {
    e.preventDefault();

    const id = el.sessionId.value;
    const data = {
      course_id: el.sessionCourse.value,
      started_at: el.sessionStart.value,
      ended_at: el.sessionEnd.value,
      effective_minutes: el.sessionEffective.value,
      note: el.sessionNote.value,
    };

    if (id) await apiPut(`/sessions/${id}`, data);
    else await apiPost("/sessions", data);

    bootstrap.Modal.getInstance(
      document.getElementById("modalAddSession")
    ).hide();

    el.formSession.reset();
    renderSessions();
    renderSummaryAndCharts();
  });

  // ============================
  // SESSION ACTION BUTTONS
  // ============================
  el.sessionsTableBody.addEventListener("click", async function (e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "del-session") {
      if (confirm("Yakin hapus sesi ini?")) {
        await apiDelete(`/sessions/${id}`);
        renderSessions();
        renderSummaryAndCharts();
      }
      return;
    }

    if (action === "edit-session") {
      const res = await apiGet("/sessions");
      const s = res.data.find((x) => x.id == id);

      el.sessionId.value = s.id;
      el.sessionCourse.value = s.course_id;
      el.sessionStart.value = s.started_at;
      el.sessionEnd.value = s.ended_at;
      el.sessionEffective.value = s.effective_minutes;
      el.sessionNote.value = s.note;

      new bootstrap.Modal(document.getElementById("modalAddSession")).show();
    }
  });

  // ======================================================
  // SUMMARY + CHARTS
  // ======================================================
  async function renderSummaryAndCharts() {
    const resSessions = await apiGet("/sessions");
    const sessions = resSessions.data || [];

    const resCourses = await apiGet("/courses");
    const courses = resCourses.data || [];

    const now = new Date();
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

    const weekSessions = sessions.filter(
      (s) => new Date(s.started_at) >= weekAgo
    );

    const totalMinutes = weekSessions.reduce(
      (sum, s) => sum + Number(s.effective_minutes),
      0
    );

    el.cardTotalHours.textContent = (totalMinutes / 60).toFixed(1);

    const totalSessionMinutes = weekSessions.reduce((sum, s) => {
      return sum + (new Date(s.ended_at) - new Date(s.started_at)) / 1000 / 60;
    }, 0);

    const rate =
      totalSessionMinutes === 0
        ? 0
        : Math.round((totalMinutes / totalSessionMinutes) * 100);

    el.cardEffectiveRate.textContent = rate + "%";

    // 14 days timeline
    const days = [];
    for (let i = 13; i >= 0; i--) {
      const d = new Date();
      d.setDate(d.getDate() - i);
      days.push(d.toISOString().slice(0, 10));
    }

    const series = {};
    courses.forEach((c) => (series[c.id] = days.map(() => 0)));

    sessions.forEach((s) => {
      const day = s.started_at.slice(0, 10);
      const idx = days.indexOf(day);
      if (idx >= 0)
        series[s.course_id][idx] += Number(s.effective_minutes) / 60;
    });

    const tsData = Object.keys(series).map((cid) => {
      const c = courses.find((x) => x.id == cid);
      return {
        label: c.title,
        data: series[cid],
        borderColor: c.color,
        backgroundColor: c.color + "55",
        fill: true,
        tension: 0.25,
      };
    });

    if (window.timeChart) window.timeChart.destroy();

    window.timeChart = new Chart(qs("#timeSeriesChart"), {
      type: "line",
      data: {
        labels: days,
        datasets: tsData,
      },
    });

    // pie chart
    const totals = courses
      .map((c) => {
        const sum = sessions
          .filter((s) => s.course_id == c.id)
          .reduce((a, b) => a + Number(b.effective_minutes) / 60, 0);

        return { name: c.title, total: sum, color: c.color };
      })
      .filter((x) => x.total > 0);

    if (window.categoryChart) window.categoryChart.destroy();

    window.categoryChart = new Chart(qs("#categoryChart"), {
      type: "pie",
      data: {
        labels: totals.map((t) => t.name),
        datasets: [
          {
            data: totals.map((t) => t.total),
            backgroundColor: totals.map((t) => t.color + "99"),
          },
        ],
      },
    });
  }

  // ======================================================
  // POMODORO TIMER
  // ======================================================
  let pomoTimer = null;
  let pomoRemaining = 25 * 60; // default 25m
  let pomoMode = "focus"; // focus / break
  let pomoRunning = false;
  let pomoStartTime = null;

  function updatePomoDisplay() {
    const m = Math.floor(pomoRemaining / 60);
    const s = pomoRemaining % 60;
    el.pomoDisplay.textContent =
      String(m).padStart(2, "0") + ":" + String(s).padStart(2, "0");

    el.pomoMode.textContent =
      pomoMode === "focus" ? "Mode: Fokus" : "Mode: Istirahat";
  }

  function startPomo() {
    if (pomoRunning) return;

    pomoRunning = true;
    pomoStartTime = Date.now();

    pomoTimer = setInterval(() => {
      pomoRemaining--;

      if (pomoRemaining <= 0) {
        clearInterval(pomoTimer);
        pomoRunning = false;

        // If focus mode ends → auto save session
        if (pomoMode === "focus") {
          autoSavePomodoroSession();
          // switch to break
          pomoMode = "break";
          pomoRemaining = 5 * 60; // 5m
        } else {
          // switch back to focus
          pomoMode = "focus";
          pomoRemaining = 25 * 60;
        }

        updatePomoDisplay();
        return;
      }

      updatePomoDisplay();
    }, 1000);
  }

  function pausePomo() {
    if (!pomoRunning) return;
    clearInterval(pomoTimer);
    pomoRunning = false;
  }

  function resetPomo() {
    clearInterval(pomoTimer);
    pomoRunning = false;

    pomoMode = "focus";
    pomoRemaining = 25 * 60;
    updatePomoDisplay();
  }

  // AUTO SAVE SESSI SETELAH Fokus selesai
  async function autoSavePomodoroSession() {
    const courseId = el.pomoCourse.value;
    if (!courseId) return;

    const startedAt = new Date(pomoStartTime).toISOString().slice(0, 16);
    const endedAt = new Date().toISOString().slice(0, 16);

    const effective = 25 * 60;

    await apiPost("/sessions", {
      course_id: courseId,
      started_at: startedAt,
      ended_at: endedAt,
      effective_minutes: effective,
      note: "Pomodoro otomatis",
    });

    renderSessions();
    renderSummaryAndCharts();
  }

  // BUTTON HANDLERS
  el.pomoStart.addEventListener("click", startPomo);
  el.pomoPause.addEventListener("click", pausePomo);
  el.pomoReset.addEventListener("click", resetPomo);

  // ============================
  // INIT
  // ============================
  document
    .getElementById("modalAddSession")
    .addEventListener("hidden.bs.modal", function () {
      el.sessionId.value = "";
      el.formSession.reset();
    });

  async function init() {
    await renderCourses();
    await renderSessions();
    await renderSummaryAndCharts();
    updatePomoDisplay();
  }

  init();
})();
// app.js — FINAL BACKEND + POMODORO VERSION (FULL FIXED)
// ======================================================

(function () {
  const API_BASE = "/trackingprogressbelajar/project-root/public/api";
  const qs = (s) => document.querySelector(s);

  // ============================
  // ELEMENT COLLECTION
  // ============================
  const el = {
    coursesList: qs("#coursesList"),
    sessionsTableBody: qs("#sessionsTableBody"),

    // course
    formCourse: qs("#formCourse"),
    courseId: qs("#courseId"),
    courseCode: qs("#courseCode"),
    courseTitle: qs("#courseTitle"),
    courseCategory: qs("#courseCategory"),
    courseColor: qs("#courseColor"),

    // session
    formSession: qs("#formSession"),
    sessionId: qs("#sessionId"),
    sessionCourse: qs("#sessionCourse"),
    sessionStart: qs("#sessionStart"),
    sessionEnd: qs("#sessionEnd"),
    sessionEffective: qs("#sessionEffective"),
    sessionNote: qs("#sessionNote"),

    // pomodoro
    pomoDisplay: qs("#pomo-display"),
    pomoMode: qs("#pomo-mode"),
    pomoCourse: qs("#pomo-course"),
    pomoStart: qs("#pomo-start"),
    pomoPause: qs("#pomo-pause"),
    pomoReset: qs("#pomo-reset"),

    cardTotalHours: qs("#card-total-hours"),
    cardEffectiveRate: qs("#card-effective-rate"),
  };

  // ============================
  // API HELPERS
  // ============================
  async function apiGet(path) {
    const r = await fetch(API_BASE + path, { credentials: "include" });
    return r.json();
  }

  async function apiPost(path, data) {
    const r = await fetch(API_BASE + path, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data),
    });
    return r.json();
  }

  async function apiPut(path, data) {
    const r = await fetch(API_BASE + path, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      credentials: "include",
      body: JSON.stringify(data),
    });
    return r.json();
  }

  async function apiDelete(path) {
    const r = await fetch(API_BASE + path, {
      method: "DELETE",
      credentials: "include",
    });
    return r.json();
  }

  // ============================
  // RENDER COURSES
  // ============================
  async function renderCourses() {
    const res = await apiGet("/courses");
    const list = res.data || [];

    el.coursesList.innerHTML = "";
    el.sessionCourse.innerHTML = "";
    el.pomoCourse.innerHTML = "";

    list.forEach((c) => {
      const li = document.createElement("li");
      li.className =
        "list-group-item d-flex justify-content-between align-items-center";

      li.innerHTML = `
        <div>
          <div class="fw-bold">${c.title}</div>
          <div class="text-muted small">${c.code} • ${c.category}</div>
        </div>
        <div class="text-end">
          <span class="badge rounded-pill" style="background:${c.color};">&nbsp;&nbsp;</span>
          <div class="mt-2">
            <button class="btn btn-sm btn-outline-secondary me-1" 
              data-action="edit-course" data-id="${c.id}">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" 
              data-action="del-course" data-id="${c.id}">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `;

      el.coursesList.appendChild(li);

      // dropdown session
      const opt = document.createElement("option");
      opt.value = c.id;
      opt.textContent = `${c.code} — ${c.title}`;
      el.sessionCourse.appendChild(opt);

      // pomodoro dropdown
      const opt2 = document.createElement("option");
      opt2.value = c.id;
      opt2.textContent = c.title;
      el.pomoCourse.appendChild(opt2);
    });
  }

  // ============================
  // RENDER SESSIONS
  // ============================
  async function renderSessions() {
    const res = await apiGet("/sessions");
    const sessions = res.data || [];

    const resCourses = await apiGet("/courses");
    const courseMap = Object.fromEntries(
      (resCourses.data || []).map((c) => [c.id, c])
    );

    el.sessionsTableBody.innerHTML = "";

    sessions.forEach((s) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `
        <td>${courseMap[s.course_id]?.title || "-"}</td>
        <td>${s.started_at}</td>
        <td>${s.ended_at}</td>
        <td>${s.effective_minutes}</td>
        <td>${s.note || ""}</td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-secondary" 
            data-action="edit-session" data-id="${s.id}">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" 
            data-action="del-session" data-id="${s.id}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;

      el.sessionsTableBody.appendChild(tr);
    });
  }

  // ============================
  // SUBMIT COURSE
  // ============================
  el.formCourse.addEventListener("submit", async function (e) {
    e.preventDefault();

    const id = el.courseId.value;
    const data = {
      code: el.courseCode.value,
      title: el.courseTitle.value,
      category: el.courseCategory.value,
      color: el.courseColor.value,
    };

    if (id) {
      await apiPut(`/courses/${id}`, data);
    } else {
      await apiPost("/courses", data);
    }

    bootstrap.Modal.getInstance(
      document.getElementById("modalAddCourse")
    ).hide();

    el.formCourse.reset();
    renderCourses();
  });

  // ============================
  // COURSE ACTION BUTTONS
  // ============================
  el.coursesList.addEventListener("click", async function (e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "del-course") {
      if (confirm("Yakin hapus mata kuliah ini?")) {
        await apiDelete(`/courses/${id}`);
        renderCourses();
      }
      return;
    }

    if (action === "edit-course") {
      const res = await apiGet("/courses");
      const c = res.data.find((x) => x.id == id);

      el.courseId.value = c.id;
      el.courseCode.value = c.code;
      el.courseTitle.value = c.title;
      el.courseCategory.value = c.category;
      el.courseColor.value = c.color;

      new bootstrap.Modal(document.getElementById("modalAddCourse")).show();
    }
  });

  // ============================
  // SUBMIT SESSION
  // ============================
  el.formSession.addEventListener("submit", async function (e) {
    e.preventDefault();

    const id = el.sessionId.value;
    const data = {
      course_id: el.sessionCourse.value,
      started_at: el.sessionStart.value,
      ended_at: el.sessionEnd.value,
      effective_minutes: el.sessionEffective.value,
      note: el.sessionNote.value,
    };

    if (id) await apiPut(`/sessions/${id}`, data);
    else await apiPost("/sessions", data);

    bootstrap.Modal.getInstance(
      document.getElementById("modalAddSession")
    ).hide();

    el.formSession.reset();
    renderSessions();
    renderSummaryAndCharts();
  });

  // ============================
  // SESSION ACTION BUTTONS
  // ============================
  el.sessionsTableBody.addEventListener("click", async function (e) {
    const btn = e.target.closest("button");
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    if (action === "del-session") {
      if (confirm("Yakin hapus sesi ini?")) {
        await apiDelete(`/sessions/${id}`);
        renderSessions();
        renderSummaryAndCharts();
      }
      return;
    }

    if (action === "edit-session") {
      const res = await apiGet("/sessions");
      const s = res.data.find((x) => x.id == id);

      el.sessionId.value = s.id;
      el.sessionCourse.value = s.course_id;
      el.sessionStart.value = s.started_at;
      el.sessionEnd.value = s.ended_at;
      el.sessionEffective.value = s.effective_minutes;
      el.sessionNote.value = s.note;

      new bootstrap.Modal(document.getElementById("modalAddSession")).show();
    }
  });

  // ======================================================
  // SUMMARY + CHARTS
  // ======================================================
  async function renderSummaryAndCharts() {
    const resSessions = await apiGet("/sessions");
    const sessions = resSessions.data || [];

    const resCourses = await apiGet("/courses");
    const courses = resCourses.data || [];

    const now = new Date();
    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);

    const weekSessions = sessions.filter(
      (s) => new Date(s.started_at) >= weekAgo
    );

    const totalMinutes = weekSessions.reduce(
      (sum, s) => sum + Number(s.effective_minutes),
      0
    );

    el.cardTotalHours.textContent = (totalMinutes / 60).toFixed(1);

    const totalSessionMinutes = weekSessions.reduce((sum, s) => {
      return sum + (new Date(s.ended_at) - new Date(s.started_at)) / 1000 / 60;
    }, 0);

    const rate =
      totalSessionMinutes === 0
        ? 0
        : Math.round((totalMinutes / totalSessionMinutes) * 100);

    el.cardEffectiveRate.textContent = rate + "%";

    // 14 days timeline
    const days = [];
    for (let i = 13; i >= 0; i--) {
      const d = new Date();
      d.setDate(d.getDate() - i);
      days.push(d.toISOString().slice(0, 10));
    }

    const series = {};
    courses.forEach((c) => (series[c.id] = days.map(() => 0)));

    sessions.forEach((s) => {
      const day = s.started_at.slice(0, 10);
      const idx = days.indexOf(day);
      if (idx >= 0)
        series[s.course_id][idx] += Number(s.effective_minutes) / 60;
    });

    const tsData = Object.keys(series).map((cid) => {
      const c = courses.find((x) => x.id == cid);
      return {
        label: c.title,
        data: series[cid],
        borderColor: c.color,
        backgroundColor: c.color + "55",
        fill: true,
        tension: 0.25,
      };
    });

    if (window.timeChart) window.timeChart.destroy();

    window.timeChart = new Chart(qs("#timeSeriesChart"), {
      type: "line",
      data: {
        labels: days,
        datasets: tsData,
      },
    });

    // pie chart
    const totals = courses
      .map((c) => {
        const sum = sessions
          .filter((s) => s.course_id == c.id)
          .reduce((a, b) => a + Number(b.effective_minutes) / 60, 0);

        return { name: c.title, total: sum, color: c.color };
      })
      .filter((x) => x.total > 0);

    if (window.categoryChart) window.categoryChart.destroy();

    window.categoryChart = new Chart(qs("#categoryChart"), {
      type: "pie",
      data: {
        labels: totals.map((t) => t.name),
        datasets: [
          {
            data: totals.map((t) => t.total),
            backgroundColor: totals.map((t) => t.color + "99"),
          },
        ],
      },
    });
  }

  // ======================================================
  // POMODORO TIMER
  // ======================================================
  let pomoTimer = null;
  let pomoRemaining = 25 * 60; // default 25m
  let pomoMode = "focus"; // focus / break
  let pomoRunning = false;
  let pomoStartTime = null;

  function updatePomoDisplay() {
    const m = Math.floor(pomoRemaining / 60);
    const s = pomoRemaining % 60;
    el.pomoDisplay.textContent =
      String(m).padStart(2, "0") + ":" + String(s).padStart(2, "0");

    el.pomoMode.textContent =
      pomoMode === "focus" ? "Mode: Fokus" : "Mode: Istirahat";
  }

  function startPomo() {
    if (pomoRunning) return;

    pomoRunning = true;
    pomoStartTime = Date.now();

    pomoTimer = setInterval(() => {
      pomoRemaining--;

      if (pomoRemaining <= 0) {
        clearInterval(pomoTimer);
        pomoRunning = false;

        // If focus mode ends → auto save session
        if (pomoMode === "focus") {
          autoSavePomodoroSession();
          // switch to break
          pomoMode = "break";
          pomoRemaining = 5 * 60; // 5m
        } else {
          // switch back to focus
          pomoMode = "focus";
          pomoRemaining = 25 * 60;
        }

        updatePomoDisplay();
        return;
      }

      updatePomoDisplay();
    }, 1000);
  }

  function pausePomo() {
    if (!pomoRunning) return;
    clearInterval(pomoTimer);
    pomoRunning = false;
  }

  function resetPomo() {
    clearInterval(pomoTimer);
    pomoRunning = false;

    pomoMode = "focus";
    pomoRemaining = 25 * 60;
    updatePomoDisplay();
  }

  // AUTO SAVE SESSI SETELAH Fokus selesai
  async function autoSavePomodoroSession() {
    const courseId = el.pomoCourse.value;
    if (!courseId) return;

    const startedAt = new Date(pomoStartTime).toISOString().slice(0, 16);
    const endedAt = new Date().toISOString().slice(0, 16);

    const effective = 25 * 60;

    await apiPost("/sessions", {
      course_id: courseId,
      started_at: startedAt,
      ended_at: endedAt,
      effective_minutes: effective,
      note: "Pomodoro otomatis",
    });

    renderSessions();
    renderSummaryAndCharts();
  }

  // BUTTON HANDLERS
  el.pomoStart.addEventListener("click", startPomo);
  el.pomoPause.addEventListener("click", pausePomo);
  el.pomoReset.addEventListener("click", resetPomo);

  // ============================
  // INIT
  // ============================
  document
    .getElementById("modalAddSession")
    .addEventListener("hidden.bs.modal", function () {
      el.sessionId.value = "";
      el.formSession.reset();
    });

  async function init() {
    await renderCourses();
    await renderSessions();
    await renderSummaryAndCharts();
    updatePomoDisplay();
  }

  init();
})();
