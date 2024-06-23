document.addEventListener('DOMContentLoaded', function () {
  var tagInput = document.getElementById('tags');
  var tagsContainer = document.createElement('div');
  tagsContainer.classList.add('tags-container');
  tagInput.parentNode.insertBefore(tagsContainer, tagInput.nextSibling);

  tagInput.addEventListener('keydown', function (event) {
    if (event.key === ' ') {
      event.preventDefault();
      var tagText = tagInput.value.trim();
      if (tagText) {
        createTag(tagText);
        tagInput.value = '';
      }
    }
  });

  function createTag(text) {
    var tag = document.createElement('span');
    tag.classList.add('tag');
    tag.textContent = text;

    var removeBtn = document.createElement('button');
    removeBtn.classList.add('remove-tag');
    removeBtn.innerHTML = '&times;';
    removeBtn.addEventListener('click', function () {
      tagsContainer.removeChild(tag);
    });

    tag.appendChild(removeBtn);
    tagsContainer.appendChild(tag);

    updateTagInput();
  }

  function updateTagInput() {
    var tags = Array.from(tagsContainer.querySelectorAll('.tag')).map(tag => tag.textContent.replace('×', '').trim());
    tagInput.value = tags.join(',');    
  }

  function loadTags(tagsString) {
    var existingTags = tagsString.split(',');
    tagsContainer.innerHTML = ''; // Limpa as tags existentes
    existingTags.forEach(function (tag) {
      if (tag.trim()) {
        createTag(tag.trim());
      }
    });
  }

  // Ensure tags are updated before submitting the form
  var form = tagInput.closest('form');
  if (form) {
    form.addEventListener('submit', function () {
      updateTagInput();
    });
  }

  // Handle the edit document modal
  var editButtons = document.querySelectorAll('.edit-document');
  editButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var tagsString = button.getAttribute('data-tags');
      loadTags(tagsString);
      tagInput.value = '';
    });
  });

  // Load existing tags if any
  var initialTagsString = tagInput.value;
  loadTags(initialTagsString);
});
