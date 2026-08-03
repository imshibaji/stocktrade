            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-white"><?= $page ? 'Edit ' . $page['title'] : 'Add New Page' ?></h1>
                    <p class="text-gray-400 mt-1"><?= $page ? 'Update page content' : 'Create a new static page' ?></p>
                </div>
                <a href="/admin/pages" class="px-4 py-2 bg-page border border-gray-600 text-gray-300 rounded-lg hover:text-white">Back to Pages</a>
            </div>

            <form method="post" action="/admin/pages/save">
                <?= csrf_field() ?>
                <?php if ($page): ?>
                    <input type="hidden" name="id" value="<?= $page['id'] ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" value="<?= esc($page['title'] ?? '') ?>" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-hidden focus:border-accent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Slug</label>
                        <input type="text" name="slug" value="<?= esc($page['slug'] ?? '') ?>" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-hidden focus:border-accent">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="<?= esc($page['meta_title'] ?? '') ?>" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-hidden focus:border-accent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Meta Description</label>
                        <input type="text" name="meta_description" value="<?= esc($page['meta_description'] ?? '') ?>" class="w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-hidden focus:border-accent">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-1">Content</label>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <button type="button" onclick="formatBlock('p')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">P</button>
                        <button type="button" onclick="formatBlock('h1')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">H1</button>
                        <button type="button" onclick="formatBlock('h2')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">H2</button>
                        <button type="button" onclick="formatBlock('h3')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">H3</button>
                        <button type="button" onclick="formatBlock('ul')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">UL</button>
                        <button type="button" onclick="formatBlock('ol')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">OL</button>
                        <button type="button" onclick="formatBlock('blockquote')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">Quote</button>
                        <button type="button" onclick="formatBlock('pre')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white">Code</button>
                    </div>
                    <div class="mb-2">
                        <button type="button" onclick="execCmd('bold')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-bold"></i></button>
                        <button type="button" onclick="execCmd('italic')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-italic"></i></button>
                        <button type="button" onclick="execCmd('underline')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-underline"></i></button>
                        <button type="button" onclick="execCmd('strikeThrough')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-strikethrough"></i></button>
                        <button type="button" onclick="execCmd('insertHorizontalRule')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-minus"></i></button>
                        <button type="button" onclick="execCmd('insertOrderedList')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-list-ol"></i></button>
                        <button type="button" onclick="execCmd('insertUnorderedList')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-list-ul"></i></button>
                        <button type="button" onclick="execCmd('formatBlock', 'code')" class="px-2 py-1 bg-page border border-gray-600 text-gray-300 text-xs rounded hover:text-white"><i class="fas fa-code"></i></button>
                    </div>
                    <div id="editor-container" class="mb-2">
                        <div id="page-editor" contenteditable="true" class="min-h-[300px] w-full px-3 py-2 bg-page border border-gray-600 rounded-lg text-white focus:outline-hidden focus:border-accent whitespace-pre-wrap"><?= $page['content'] ?? '' ?></div>
                        <textarea name="content" id="page-content" class="hidden"></textarea>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Use the toolbar above to format content. HTML is supported.</p>
                </div>

                <script>
                    const editor = document.getElementById('page-editor');
                    const textarea = document.getElementById('page-content');
                    function updateTextarea() {
                        textarea.value = editor.innerHTML;
                    }
                    editor.addEventListener('input', updateTextarea);
                    editor.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && e.shiftKey) {
                            e.preventDefault();
                            document.execCommand('insertLineBreak');
                        }
                    });
                    function execCmd(cmd, value = null) {
                        document.execCommand(cmd, false, value);
                        editor.focus();
                        updateTextarea();
                    }
                    function formatBlock(tag) {
                        const range = document.getSelection().getRangeAt(0);
                        if (range.startContainer.nodeType === Node.ELEMENT_NODE) {
                            const parent = range.startContainer.parentElement;
                            const block = parent.closest(tag);
                            if (block) {
                                document.execCommand('formatBlock', false, tag);
                            } else {
                                document.execCommand('formatBlock', false, tag);
                            }
                        } else {
                            document.execCommand('formatBlock', false, tag);
                        }
                        editor.focus();
                        updateTextarea();
                    }
                    updateTextarea();
                </script>

                <div class="mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" <?= !empty($page['is_active']) ? 'checked' : '' ?>>
                        <span class="text-sm text-gray-300">Active (visible on frontend)</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="px-6 py-2 bg-accent text-on-accent font-medium rounded-lg hover:bg-accent-2 transition">Save Page</button>
                </div>
            </form>
