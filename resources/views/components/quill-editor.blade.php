@once
  @push('assets')
    <link href="https://cdn.jsdelivr.net/npm/quill/dist/quill.snow.css" rel="stylesheet">
    <style>
      .quill-editor {
        height: 300px;
      }
    </style>
  @endpush

  @push('js')
    <script src="https://cdn.jsdelivr.net/npm/quill/dist/quill.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.quill-editor').forEach(function(el) {
          const hidden = el.parentElement.querySelector('.quill-editor-hidden');

          function imageHandler() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
              const file = input.files[0];
              if (file) {
                let formData = new FormData();
                formData.append('image', file);

                try {
                  const res = await fetch("{{ route('picture.upload') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                  });

                  const data = await res.json();
                  if (data.url) {
                    const range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', data.url);
                    quill.setSelection(range.index + 1);
                  }
                } catch (err) {
                  toast(alert_created_text("Failed to upload image."), "error");
                  console.error("Upload gagal", err);
                }
              }
            };
          }

          const quill = new Quill(el, {
            theme: 'snow',
            modules: {
              toolbar: {
                container: [
                  [{
                    header: [1, 2, 3, false]
                  }],
                  ['bold', 'italic', 'underline'],
                  [{
                    list: 'ordered'
                  }, {
                    list: 'bullet'
                  }],
                  ['link', 'image', 'blockquote'],
                  ['code-block']
                ],
                handlers: {
                  image: imageHandler
                }
              }
            }
          });

          if (hidden.value) {
            quill.root.innerHTML = hidden.value;
          } else {
            hidden.value = quill.root.innerHTML;
          }

          quill.on('text-change', function() {
            const div = document.createElement('div');
            div.innerHTML = quill.root.innerHTML;
            div.querySelectorAll('[style]').forEach(el => {
              el.style.color = '';
              if (!el.getAttribute('style')) el.removeAttribute('style');
            });
            hidden.value = div.innerHTML;
          });
        });
      });
    </script>
  @endpush
@endonce
