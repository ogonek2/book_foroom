// Lexical Editor - Simplified integration for Vue/Laravel
// Using Lexical with plain JavaScript (no React)

let lexicalEditorInstance = null;

export function initLexicalEditor(editorElement, contentInput, options = {}) {
    // For now, we'll use a simpler approach
    // Lexical requires React for full functionality
    // We'll create a wrapper that uses contenteditable with Lexical-like features
    
    console.log('Lexical Editor initialization - using simplified approach');
    
    // Return a simple editor interface that mimics Lexical API
    return {
        editor: {
            getEditorState: () => ({
                read: (callback) => {
                    callback();
                }
            }),
            getRootElement: () => editorElement,
            setRootElement: (element) => {
                // Editor is already set
            },
            update: (callback) => {
                callback();
            },
            setEditable: (editable) => {
                if (editorElement) {
                    editorElement.contentEditable = editable;
                }
            },
            registerUpdateListener: (listener) => {
                if (editorElement) {
                    editorElement.addEventListener('input', () => {
                        listener({ editorState: { read: (cb) => cb() } });
                    });
                }
            }
        },
        getHTML: () => {
            return editorElement ? editorElement.innerHTML : '';
        },
        getText: () => {
            return editorElement ? (editorElement.innerText || editorElement.textContent) : '';
        },
        setContent: (content) => {
            if (editorElement) {
                editorElement.innerHTML = content;
            }
        }
    };
}

// Lexical Editor Setup
export function initLexicalEditor(editorElement, contentInput, options = {}) {
    const {
        onUpdate = () => {},
        placeholder = '',
        mentions = [],
        hashtags = []
    } = options;

    // Create editor configuration
    const editorConfig = {
        namespace: 'DiscussionEditor',
        theme: {
            text: {
                bold: 'font-bold',
                italic: 'italic',
                underline: 'underline',
                strikethrough: 'line-through',
                code: 'font-mono bg-slate-100 dark:bg-slate-800 px-1 rounded',
            },
            link: 'text-indigo-600 dark:text-indigo-400 underline',
            list: {
                nested: {
                    listitem: 'ml-4',
                },
                ol: 'list-decimal ml-6',
                ul: 'list-disc ml-6',
            },
            paragraph: 'mb-2',
            heading: {
                h1: 'text-3xl font-bold mb-4',
                h2: 'text-2xl font-bold mb-3',
                h3: 'text-xl font-bold mb-2',
            },
            mention: 'mention bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-1.5 py-0.5 rounded font-medium',
            hashtag: 'hashtag text-indigo-600 dark:text-indigo-400 font-semibold',
        },
        onError: (error) => {
            console.error('Lexical Editor Error:', error);
        },
        nodes: [],
    };

    // Create editor
    const editor = createEditor(editorConfig);

    // Register plugins
    registerRichText(editor);
    registerLink(editor);
    registerList(editor);

    // Update content handler
    const updateContent = () => {
        editor.getEditorState().read(() => {
            const root = $getRoot();
            const html = root.getTextContent();
            const json = JSON.stringify(editor.getEditorState().toJSON());
            
            // Update hidden textarea
            if (contentInput) {
                contentInput.value = html;
            }
            
            // Call custom update handler
            onUpdate(html, json);
        });
    };

    // Set initial content
    if (contentInput && contentInput.value) {
        editor.setEditable(false);
        // Parse and set initial content
        // For now, we'll set it as plain text
        editor.update(() => {
            const root = $getRoot();
            root.clear();
            const paragraph = $createParagraphNode();
            paragraph.append($createTextNode(contentInput.value));
            root.append(paragraph);
        });
        editor.setEditable(true);
    }

    // Listen for updates
    editor.registerUpdateListener(({ editorState }) => {
        editorState.read(() => {
            updateContent();
        });
    });

    // Handle mentions
    const handleMentions = () => {
        // This will be implemented with a custom node
        // For now, we'll handle it in the update listener
    };

    // Handle hashtags
    const handleHashtags = () => {
        // This will be implemented with a custom node
        // For now, we'll handle it in the update listener
    };

    // Mount editor to DOM
    editor.setRootElement(editorElement);

    return {
        editor,
        updateContent,
        getHTML: () => {
            let html = '';
            editor.getEditorState().read(() => {
                const root = $getRoot();
                html = root.getTextContent();
            });
            return html;
        },
        getText: () => {
            let text = '';
            editor.getEditorState().read(() => {
                const root = $getRoot();
                text = root.getTextContent();
            });
            return text;
        },
        setContent: (content) => {
            editor.update(() => {
                const root = $getRoot();
                root.clear();
                const paragraph = $createParagraphNode();
                paragraph.append($createTextNode(content));
                root.append(paragraph);
            });
        }
    };
}

