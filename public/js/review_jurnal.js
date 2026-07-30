const WEBHOOK_URL =
  "https://n8n-idw9vfy3qzrq.arman.sumopod.my.id/webhook/ea6d99de-adc4-45cd-8f8c-01a413bf3a19";

let lastResultText = "";
let resultFileName = "hasil-review-jurnal";
let reviewCount = 0;
const FREE_REVIEW_LIMIT = 2;

/* =========================
   UTILITIES
========================= */

function cleanText(text) {
  if (!text) return "";
  return text
    .replace(/[*_`~]/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

function sanitizeFileName(text) {
  if (!text) return "hasil-review-jurnal";

  return text
    .toLowerCase()
    .replace(/[*_`~]/g, "")
    .replace(/[^a-z0-9\s-]/gi, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-")
    .substring(0, 80)
    .replace(/^-|-$/g, "");
}

/* =========================
   STORAGE
========================= */

function saveToStorage() {
  localStorage.setItem("review_jurnal_text", lastResultText);
  localStorage.setItem("review_jurnal_filename", resultFileName);
  localStorage.setItem("review_jurnal_count", reviewCount.toString());
}

function loadFromStorage() {
  const savedText = localStorage.getItem("review_jurnal_text");
  const savedFileName = localStorage.getItem("review_jurnal_filename");
  const savedCount = localStorage.getItem("review_jurnal_count");

  if (savedText) {
    lastResultText = savedText;
    document.getElementById("result").textContent = savedText;
    document.getElementById("downloadBtn").disabled = false;
  }

  if (savedFileName) {
    resultFileName = savedFileName;
  }

  if (savedCount) {
    reviewCount = parseInt(savedCount, 10) || 0;
  }
}

/* =========================
   VALIDATION
========================= */

function validatePDF(file) {
  if (file.type !== "application/pdf") {
    alert("File harus berformat PDF.");
    return false;
  }

  if (file.size > 20 * 1024 * 1024) {
    alert("Ukuran file maksimal 20 MB.");
    return false;
  }

  return true;
}

/* =========================
   PASSWORD CHECK
========================= */

function validateUploadPassword() {
  const currentHour = new Date().getHours().toString();
  const inputPassword = prompt(
    "Untuk tambahan kouta minimal traktir kopi🤣"
  );

  if (inputPassword === null) return false;

  if (inputPassword.trim() !== currentHour) {
    alert("Password salah. Password adalah jam saat ini (24 jam).");
    return false;
  }

  return true;
}

/* =========================
   MAIN FUNCTION
========================= */

async function uploadPDF() {

  // === KUNCI SETELAH MELEWATI BATAS GRATIS ===
  if (reviewCount >= FREE_REVIEW_LIMIT) {
    if (!validateUploadPassword()) return;
  }

  const fileInput = document.getElementById("pdfInput");
  const resultBox = document.getElementById("result");
  const loading = document.getElementById("loading");
  const downloadBtn = document.getElementById("downloadBtn");

  if (!fileInput.files.length) {
    alert("Silakan pilih file PDF terlebih dahulu.");
    return;
  }

  const pdfFile = fileInput.files[0];
  if (!validatePDF(pdfFile)) return;

  const formData = new FormData();
  formData.append("data", pdfFile);

  loading.classList.remove("hidden");
  resultBox.textContent = "";
  downloadBtn.disabled = true;

  const controller = new AbortController();
  const timeoutId = setTimeout(() => controller.abort(), 120000);

  try {
    const response = await fetch(WEBHOOK_URL, {
      method: "POST",
      body: formData,
      signal: controller.signal,
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      throw new Error(`Server error (${response.status})`);
    }

    const contentType = response.headers.get("content-type");

    if (contentType && contentType.includes("application/json")) {
      const json = await response.json();

      if (json.judul) {
        resultFileName = sanitizeFileName(json.judul);
      }

      lastResultText = `
Judul:
${cleanText(json.judul)}

Jurnal:
${cleanText(json.jurnal)}

Tahun:
${cleanText(json.tahun)}

Penulis:
${cleanText(json.penulis)}

Tujuan Penelitian:
${cleanText(json.tujuan_penelitian)}

Subjek Penelitian:
${cleanText(json.subjek_penelitian)}

Metode Penelitian:
${cleanText(json.metode_penelitian)}

Hasil Penelitian:
${cleanText(json.hasil_penelitian)}

Kekuatan:
${cleanText(json.kekuatan)}

Kelemahan:
${cleanText(json.kelemahan)}

Rekomendasi Penelitian Lanjutan:
${cleanText(json.rekomendasi_penelitian_lanjutan)}
      `.trim();

    } else {
      lastResultText = cleanText(await response.text());
    }

    // === HITUNG REVIEW BERHASIL ===
    reviewCount++;
    resultBox.textContent = lastResultText;
    downloadBtn.disabled = false;
    saveToStorage();

  } catch (err) {
    if (err.name === "AbortError") {
      resultBox.textContent =
        "Proses terlalu lama. Silakan coba kembali atau gunakan file yang lebih kecil.";
    } else {
      resultBox.textContent = "Terjadi kesalahan:\n" + err.message;
    }
  } finally {
    loading.classList.add("hidden");
  }
}

/* =========================
   DOWNLOAD (BEBAS)
========================= */

function downloadResult() {
  if (!lastResultText) {
    alert("Belum ada hasil untuk diunduh.");
    return;
  }

  const blob = new Blob([lastResultText], {
    type: "text/plain;charset=utf-8",
  });

  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");

  a.href = url;
  a.download = `${resultFileName}.txt`;

  document.body.appendChild(a);
  a.click();

  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

/* =========================
   INIT
========================= */

document.addEventListener("DOMContentLoaded", () => {
  const loading = document.getElementById("loading");
  if (loading) loading.classList.add("hidden");

  loadFromStorage();
});
