// import bootstrap, adminlte, sweetalert, aplinejs,
import "bootstrap";
import "admin-lte";
import "./bootstrap";

import Swal from 'sweetalert2';
window.Swal = Swal; 


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Theme mode Script 

const storageKey = "lte-theme";
const root = document.documentElement;

const getPreferredTheme = () =>
    window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";

const resolveTheme = (theme) => (theme === "auto" ? getPreferredTheme() : theme);

const applyTheme = (theme) => {
    const resolved = resolveTheme(theme);

    root.setAttribute("data-bs-theme", resolved);
    root.style.colorScheme = resolved;
    localStorage.setItem(storageKey, theme);

    updateButtons(theme);
};

const updateButtons = (activeTheme) => {
    document.querySelectorAll("[data-bs-theme-value]").forEach((btn) => {
        const isActive = btn.getAttribute("data-bs-theme-value") === activeTheme;
        btn.classList.toggle("active", isActive);
        btn.setAttribute("aria-pressed", isActive ? "true" : "false");

        const check = btn.querySelector(".bi-check-lg");
        if (check) {
            check.classList.toggle("d-none", !isActive);
        }
    });

    document.querySelectorAll("[data-lte-theme-icon]").forEach((icon) => {
        const shouldShow = icon.getAttribute("data-lte-theme-icon") === activeTheme;
        icon.classList.toggle("d-none", !shouldShow);
    });
};

const initTheme = () => {
    document.querySelectorAll("[data-bs-theme-value]").forEach((btn) => {
        btn.addEventListener("click", () => {
            applyTheme(btn.getAttribute("data-bs-theme-value"));
        });
    });

    const stored = localStorage.getItem(storageKey);
    const initialTheme =
        stored === "dark" || stored === "light" || stored === "auto"
            ? stored
            : "auto";

    applyTheme(initialTheme);
};

document.addEventListener("DOMContentLoaded", initTheme);