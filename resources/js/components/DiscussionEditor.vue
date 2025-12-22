<template>
    <div class="discussion-editor">
        <!-- Toolbar -->
        <div class="tiptap-toolbar" v-if="editor">
            <button
                type="button"
                @click="editor.chain().focus().toggleBold().run()"
                :class="{ 'is-active': editor.isActive('bold') }"
                title="Жирний (Ctrl+B)"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6zM6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z" />
                </svg>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleItalic().run()"
                :class="{ 'is-active': editor.isActive('italic') }"
                title="Курсив (Ctrl+I)"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleUnderline().run()"
                :class="{ 'is-active': editor.isActive('underline') }"
                title="Підкреслений (Ctrl+U)"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </button>
            <div class="divider"></div>
            <button
                type="button"
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'is-active': editor.isActive('bulletList') }"
                title="Маркований список"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'is-active': editor.isActive('orderedList') }"
                title="Нумерований список"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                </svg>
            </button>
            <div class="divider"></div>
            <button
                type="button"
                @click="showLinkModal"
                :class="{ 'is-active': editor.isActive('link') }"
                title="Посилання (Ctrl+K)"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
            </button>
            <button
                type="button"
                @click="editor.chain().focus().toggleCode().run()"
                :class="{ 'is-active': editor.isActive('code') }"
                title="Код"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>
            </button>
            <div class="divider"></div>
            <button
                type="button"
                @click="showImageModal"
                title="Додати зображення"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </button>
        </div>

        <!-- Editor - TipTap uses contenteditable div, NOT textarea -->
        <div class="relative">
            <editor-content :editor="editor" class="tiptap-editor" />
        </div>

        <!-- Link Modal -->
        <div v-if="showLinkDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeLinkModal">
            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Додати посилання</h3>
                <input
                    v-model="linkUrl"
                    type="url"
                    placeholder="https://example.com"
                    class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    @keyup.enter="insertLink"
                />
                <div class="flex gap-2 mt-4">
                    <button
                        @click="insertLink"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
                    >
                        Додати
                    </button>
                    <button
                        @click="closeLinkModal"
                        class="flex-1 px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white rounded-lg transition-colors"
                    >
                        Скасувати
                    </button>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div v-if="showImageDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeImageModal">
            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Додати зображення</h3>
                
                <!-- Tabs for upload method -->
                <div class="flex gap-2 mb-4 border-b border-slate-200 dark:border-slate-700">
                    <button
                        @click="imageUploadMethod = 'file'"
                        :class="['px-4 py-2 text-sm font-medium transition-colors', imageUploadMethod === 'file' ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']"
                    >
                        З галереї
                    </button>
                    <button
                        @click="imageUploadMethod = 'url'"
                        :class="['px-4 py-2 text-sm font-medium transition-colors', imageUploadMethod === 'url' ? 'text-indigo-600 dark:text-indigo-400 border-b-2 border-indigo-600 dark:border-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white']"
                    >
                        За посиланням
                    </button>
                </div>

                <!-- File Upload -->
                <div v-if="imageUploadMethod === 'file'">
                    <input
                        ref="imageFileInput"
                        type="file"
                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                        @change="handleImageFileSelect"
                        class="hidden"
                    />
                    <button
                        @click="$refs.imageFileInput.click()"
                        class="w-full px-4 py-3 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors text-slate-700 dark:text-slate-300"
                    >
                        <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm">Натисніть для вибору зображення</span>
                    </button>
                    <div v-if="imageUploading" class="mt-4 text-center">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Завантаження...</p>
                    </div>
                    <div v-if="imagePreview" class="mt-4">
                        <img :src="imagePreview" alt="Preview" class="w-full rounded-lg max-h-48 object-contain" />
                    </div>
                </div>

                <!-- URL Input -->
                <div v-if="imageUploadMethod === 'url'">
                    <input
                        v-model="imageUrl"
                        type="url"
                        placeholder="https://example.com/image.jpg"
                        class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        @keyup.enter="insertImage"
                    />
                </div>

                <div class="flex gap-2 mt-4">
                    <button
                        @click="insertImage"
                        :disabled="imageUploading || (!imageUrl && !imagePreview)"
                        class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
                    >
                        Додати
                    </button>
                    <button
                        @click="closeImageModal"
                        class="flex-1 px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white rounded-lg transition-colors"
                    >
                        Скасувати
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-2';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Mention from '@tiptap/extension-mention';
import Underline from '@tiptap/extension-underline';
import Placeholder from '@tiptap/extension-placeholder';
import { Plugin, PluginKey } from '@tiptap/pm/state';
import { Mark } from '@tiptap/core';

// Custom Hashtag extension with auto-highlighting
const Hashtag = Mark.create({
    name: 'hashtag',
    addOptions() {
        return {
            HTMLAttributes: {
                class: 'hashtag text-indigo-600 dark:text-indigo-400 font-semibold',
            },
        };
    },
    parseHTML() {
        return [
            {
                tag: 'span.hashtag',
            },
        ];
    },
    renderHTML({ HTMLAttributes }) {
        return ['span', this.options.HTMLAttributes, 0];
    },
    addCommands() {
        return {
            setHashtag: () => ({ commands }) => {
                return commands.setMark(this.name);
            },
            toggleHashtag: () => ({ commands }) => {
                return commands.toggleMark(this.name);
            },
            unsetHashtag: () => ({ commands }) => {
                return commands.unsetMark(this.name);
            },
        };
    },
});

// Plugin for auto-highlighting hashtags
const HashtagPlugin = new Plugin({
    key: new PluginKey('hashtag'),
    appendTransaction: (transactions, oldState, newState) => {
        const { tr } = newState;
        let modified = false;

        if (transactions.some(transaction => transaction.docChanged)) {
            newState.doc.descendants((node, pos) => {
                if (node.isText) {
                    const text = node.text;
                    // Match hashtags - supports Cyrillic
                    const hashtagRegex = /#([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_]+)/g;
                    let match;

                    while ((match = hashtagRegex.exec(text)) !== null) {
                        const start = pos + match.index;
                        const end = start + match[0].length;
                        const hashtagText = match[1];

                        // Check if already marked
                        const existingMark = node.marks.find(m => m.type.name === 'hashtag');
                        if (!existingMark) {
                            tr.addMark(start, end, newState.schema.marks.hashtag.create({
                                'data-tag': hashtagText
                            }));
                            modified = true;
                        }
                    }
                }
            });
        }

        return modified ? tr : null;
    },
});

export default {
    name: 'DiscussionEditor',
    components: {
        EditorContent,
    },
    props: {
        modelValue: {
            type: String,
            default: '',
        },
        placeholder: {
            type: String,
            default: 'Опишіть тему для обговорення. Використовуйте @ для згадки користувачів та # для хештегів...',
        },
    },
    data() {
        return {
            editor: null,
            showLinkDialog: false,
            showImageDialog: false,
            linkUrl: '',
            imageUrl: '',
            imageUploadMethod: 'file', // 'file' or 'url'
            imageUploading: false,
            imagePreview: null,
        };
    },
    mounted() {
        this.initEditor();
    },
    beforeDestroy() {
        if (this.editor) {
            this.editor.destroy();
        }
    },
    methods: {
        initEditor() {
            this.editor = new Editor({
                extensions: [
                    StarterKit.configure({
                        heading: false,
                        codeBlock: false,
                        blockquote: false,
                    }),
                    Underline,
                    Placeholder.configure({
                        placeholder: this.placeholder,
                    }),
                    Link.configure({
                        openOnClick: false,
                        HTMLAttributes: {
                            class: 'text-indigo-600 dark:text-indigo-400 underline',
                        },
                    }),
                    Image.configure({
                        inline: true,
                        allowBase64: true,
                        HTMLAttributes: {
                            class: 'max-w-full h-auto rounded-lg',
                        },
                    }),
                    Mention.configure({
                        HTMLAttributes: {
                            class: 'mention bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-1.5 py-0.5 rounded font-medium',
                        },
                        suggestion: {
                            items: async ({ query }) => {
                                return await this.searchUsers(query);
                            },
                            render: () => {
                                let component;
                                let popup;

                                return {
                                    onStart: (props) => {
                                        // Create popup element
                                        popup = document.createElement('div');
                                        popup.className = 'mention-dropdown';
                                        document.body.appendChild(popup);

                                        // Position near cursor
                                        this.positionMentionDropdown(props, popup);
                                    },
                                    onUpdate: (props) => {
                                        this.positionMentionDropdown(props, popup);
                                        this.renderMentionDropdown(props, popup);
                                    },
                                    onKeyDown: (props) => {
                                        if (props.event.key === 'ArrowUp') {
                                            props.upHandler();
                                            return true;
                                        }
                                        if (props.event.key === 'ArrowDown') {
                                            props.downHandler();
                                            return true;
                                        }
                                        if (props.event.key === 'Enter') {
                                            props.enterHandler();
                                            return true;
                                        }
                                        if (props.event.key === 'Escape') {
                                            props.hide();
                                            return true;
                                        }
                                        return false;
                                    },
                                    onExit: () => {
                                        if (popup && popup.parentNode) {
                                            popup.parentNode.removeChild(popup);
                                        }
                                    },
                                };
                            },
                        },
                    }),
                    Hashtag.extend({
                        addProseMirrorPlugins() {
                            return [HashtagPlugin];
                        },
                    }),
                ],
                content: this.modelValue,
                onUpdate: ({ editor }) => {
                    const html = editor.getHTML();
                    this.$emit('update:modelValue', html);
                    this.$emit('input', html);
                    this.$emit('change', html);
                },
                editorProps: {
                    attributes: {
                        class: 'tiptap-editor',
                        'data-placeholder': this.placeholder,
                        contenteditable: 'true',
                    },
                },
                onCreate: ({ editor }) => {
                    // Update content counter on editor creation
                    this.$nextTick(() => {
                        this.$emit('change', editor.getHTML());
                    });
                },
            });
        },
        async searchUsers(query) {
            try {
                const response = await fetch(`/api/users/search?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    const users = (data.users || []).slice(0, 10);
                    return users.map(user => ({
                        id: user.username,
                        label: user.name || user.username,
                        username: user.username,
                        avatar: user.avatar_display || user.avatar,
                        name: user.name,
                    }));
                }
            } catch (error) {
                console.error('Error loading users:', error);
            }
            return [];
        },
        positionMentionDropdown(props, popup) {
            const { range } = props;
            const coords = props.clientRect();
            
            if (coords) {
                popup.style.position = 'absolute';
                popup.style.top = `${coords.bottom + window.scrollY}px`;
                popup.style.left = `${coords.left + window.scrollX}px`;
                popup.style.zIndex = '1000';
            }
        },
        renderMentionDropdown(props, popup) {
            const { items, command } = props;
            
            popup.innerHTML = items.map((item, index) => {
                const isSelected = index === props.selectedIndex;
                const avatar = item.avatar || '';
                const initials = (item.name || item.username || 'U').charAt(0).toUpperCase();
                
                return `
                    <div class="mention-item ${isSelected ? 'is-selected' : ''}" data-index="${index}">
                        <div class="mention-item-avatar">
                            ${avatar ? `<img src="${avatar}" alt="${item.name || item.username}" class="w-full h-full rounded-full object-cover">` : initials}
                        </div>
                        <div class="mention-item-info">
                            <div class="mention-item-name">${item.name || item.username || 'Користувач'}</div>
                            <div class="mention-item-username">@${item.username || ''}</div>
                        </div>
                    </div>
                `;
            }).join('');

            // Add click handlers
            popup.querySelectorAll('.mention-item').forEach((item, index) => {
                item.addEventListener('click', () => {
                    command(items[index]);
                });
            });
        },
        showLinkModal() {
            const previousUrl = this.editor.getAttributes('link').href;
            this.linkUrl = previousUrl || '';
            this.showLinkDialog = true;
        },
        closeLinkModal() {
            this.showLinkDialog = false;
            this.linkUrl = '';
            this.editor.chain().focus().run();
        },
        insertLink() {
            if (this.linkUrl) {
                this.editor.chain().focus().extendMarkRange('link').setLink({ href: this.linkUrl }).run();
            } else {
                this.editor.chain().focus().extendMarkRange('link').unsetLink().run();
            }
            this.closeLinkModal();
        },
        showImageModal() {
            this.imageUrl = '';
            this.imagePreview = null;
            this.imageUploadMethod = 'file';
            this.showImageDialog = true;
        },
        closeImageModal() {
            this.showImageDialog = false;
            this.imageUrl = '';
            this.imagePreview = null;
            this.imageUploading = false;
            // Reset file input
            if (this.$refs.imageFileInput) {
                this.$refs.imageFileInput.value = '';
            }
            this.editor.chain().focus().run();
        },
        async handleImageFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.match('image.*')) {
                alert('Будь ласка, виберіть файл зображення', 'Помилка валідації', 'error');
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Розмір файлу не повинен перевищувати 2MB', 'Помилка валідації', 'error');
                return;
            }

            this.imageUploading = true;
            this.imagePreview = URL.createObjectURL(file);

            try {
                const formData = new FormData();
                formData.append('image', file);

                const response = await fetch('/api/images/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (data.success && data.url) {
                    this.imageUrl = data.url;
                } else {
                    alert(data.message || 'Помилка завантаження зображення', 'Помилка', 'error');
                    this.imagePreview = null;
                    this.imageUrl = '';
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                alert('Помилка завантаження зображення', 'Помилка', 'error');
                this.imagePreview = null;
                this.imageUrl = '';
            } finally {
                this.imageUploading = false;
            }
        },
        insertImage() {
            const imageSrc = this.imageUrl || this.imagePreview;
            if (imageSrc) {
                this.editor.chain().focus().setImage({ src: imageSrc }).run();
            }
            this.closeImageModal();
        },
    },
    watch: {
        modelValue(value) {
            if (this.editor && value !== this.editor.getHTML()) {
                this.editor.commands.setContent(value, false);
            }
        },
    },
};
</script>

<style>
/* Styles are in the parent component */
</style>
