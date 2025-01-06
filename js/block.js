const { registerBlockType } = wp.blocks;
const { TextControl } = wp.components;
const { useBlockProps } = wp.blockEditor;

registerBlockType('github-repo-card/repo', {
    title: 'GitHub 仓库卡片',
    icon: {
        src: <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fillRule="evenodd" clipRule="evenodd" d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z" 
            fill="currentColor"/>
        </svg>,
        foreground: '#24292e'
    },
    category: 'widgets',
    keywords: [ 'github', 'repository', 'card' ],
    attributes: {
        user: {
            type: 'string',
            default: ''
        },
        repo: {
            type: 'string',
            default: ''
        }
    },

    edit: function(props) {
        const blockProps = useBlockProps();
        const { attributes, setAttributes } = props;

        return (
            <div {...blockProps}>
                <div className="github-repo-card-editor">
                    <h4>GitHub 仓库卡片</h4>
                    <TextControl
                        label="GitHub 用户名"
                        value={attributes.user}
                        onChange={(value) => setAttributes({ user: value })}
                    />
                    <TextControl
                        label="仓库名称"
                        value={attributes.repo}
                        onChange={(value) => setAttributes({ repo: value })}
                    />
                    {attributes.user && attributes.repo ? (
                        <div className="preview-note">
                            将显示: {attributes.user}/{attributes.repo}
                        </div>
                    ) : (
                        <div className="preview-note">
                            请输入 GitHub 用户名和仓库名
                        </div>
                    )}
                </div>
            </div>
        );
    },

    save: function(props) {
        const { attributes } = props;
        return `[github_repo user="${attributes.user}" repo="${attributes.repo}"]`;
    }
}); 