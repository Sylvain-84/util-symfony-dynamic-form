console.log('dyna do')

document.addEventListener("DOMContentLoaded", function () {
  const categorySelect = document.getElementById("dynamic_category");
  const sectionSelect = document.getElementById("dynamic_section");

  categorySelect.addEventListener("change", function () {
    const selectedCategoryId = this.value;

    // Make an AJAX request to get sections for the selected category
    fetch(`/dynamic/get-sections/${selectedCategoryId}`)
      .then((response) => response.json())
      .then((data) => {
        // Clear existing options
        sectionSelect.innerHTML = "";

        // Populate section dropdown with new options
        data.sections.forEach(function (section) {
          const option = document.createElement("option");
          option.value = section.id;
          option.text = section.name;
          sectionSelect.appendChild(option);
        });
      });
  });
});