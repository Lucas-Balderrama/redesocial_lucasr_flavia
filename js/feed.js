document.addEventListener("DOMContentLoaded", () => {
    const userBtn = document.getElementById("user-btn");
    const modal = document.getElementById("user-modal");
    const closeBtn = document.getElementById("close-modal");

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }


    if (userBtn && modal) {
        userBtn.addEventListener("click", () => {
            modal.style.display = modal.style.display === "block" ? "none" : "block";
        });

        window.addEventListener("click", (e) => {
            if (!modal.contains(e.target) && !userBtn.contains(e.target)) {
                modal.style.display = "none";
            }
        });
    }
});
