const sidebarFooterToggleBtn = document.querySelector(".sidebar-footer.toggle-btn");
const sidebarOnMouse = document.querySelector(".toggle-btn");
const sidebar = document.querySelector("#sidebar");
const toggleIcon = document.querySelector("#toggle-icon");

let isExpanded = sidebar.classList.contains('expand');

sidebarFooterToggleBtn.addEventListener("click", function () {
  sidebar.classList.toggle("expand");
  isExpanded = !isExpanded;
  updateToggleIcon();
});

sidebarOnMouse.addEventListener("mouseenter", function () {
  sidebar.classList.add("expand");
  isExpanded = true;
  updateToggleIcon();
});

sidebarOnMouse.addEventListener("mouseleave", function () {
  sidebar.classList.remove("expand");
  isExpanded = false;
  updateToggleIcon();
});


function updateToggleIcon() {
  if (isExpanded) {
    toggleIcon.classList.remove('fa-chevron-right');
    toggleIcon.classList.add('fa-chevron-left');
  } else {
    toggleIcon.classList.remove('fa-chevron-left');
    toggleIcon.classList.add('fa-chevron-right');
  }
}
/*

window.addEventListener('load', checkScreenWidthAndToggle);
window.addEventListener('resize', checkScreenWidthAndToggle);
*/
