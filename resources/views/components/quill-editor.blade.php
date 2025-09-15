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
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.quill-editor').forEach(function (el) {
                    const hidden = el.parentElement.querySelector('.quill-editor-hidden');

                    const quill = new Quill(el, {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                ['link', 'image', 'blockquote'],
                                ['code-block']
                            ]
                        }
                    });

                    if (hidden.value) {
                        quill.root.innerHTML = hidden.value;
                    } else {
                        hidden.value = quill.root.innerHTML;
                    }

                    quill.on('text-change', function () {
                        hidden.value = quill.root.innerHTML;
                    });
                });
            });
        </script>
    @endpush
@endonce
