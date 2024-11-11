(function(wp) {
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { Fragment } = wp.element;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl } = wp.components;
    const { __ } = wp.i18n;

    const SIZE_MAPPING = {
        'has-small-font-size': 'has-text-s',
        'has-medium-font-size': 'has-text-m',
        'has-large-font-size': 'has-text-l',
        'has-x-large-font-size': 'has-text-xl',
        'has-xx-large-font-size': 'has-text-xxl'
    };

    const TEXT_SIZES = [
        { label: __('Default'), value: '' },
        { label: 'XXS', value: 'xxs' },
        { label: 'XS', value: 'xs' },
        { label: 'S', value: 's' },
        { label: 'M', value: 'm' },
        { label: 'L', value: 'l' },
        { label: 'XL', value: 'xl' },
        { label: 'XXL', value: 'xxl' }
    ];

    const addTextSizeControl = createHigherOrderComponent((BlockEdit) => {
        return (props) => {
            if (!['core/paragraph', 'core/heading'].includes(props.name)) {
                return <BlockEdit {...props} />;
            }

            const { attributes, setAttributes } = props;
            const { className, fontSize } = attributes;

            const getCurrentSize = () => {
                if (!className && !fontSize) return '';
                
                if (fontSize) {
                    const wpClass = `has-${fontSize}-font-size`;
                    if (SIZE_MAPPING[wpClass]) {
                        return SIZE_MAPPING[wpClass].replace('has-text-', '');
                    }
                }

                if (className) {
                    const match = className.match(/has-text-(xxs|xs|s|m|l|xl|xxl)/);
                    return match ? match[1] : '';
                }

                return '';
            };

            const onChangeTextSize = (newSize) => {
                const classNames = className ? className.split(' ').filter(name => {
                    return !name.match(/has-text-(xxs|xs|s|m|l|xl|xxl)/) && 
                           !name.match(/has-(small|medium|large|x-large|xx-large)-font-size/);
                }) : [];

		
                const newAttributes = {
                    fontSize: undefined
                };

                if (newSize) {
                    classNames.push(`has-text-${newSize}`);
                }
                
                newAttributes.className = classNames.length > 0 ? classNames.join(' ') : undefined;
                
                setAttributes(newAttributes);
                
                console.log('Text size changed:', {
                    newSize,
                    oldClassName: className,
                    oldFontSize: fontSize,
                    newAttributes
                });
            };

            return (
                <Fragment>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Text Size Settings')}>
                            <SelectControl
                                label={__('Size')}
                                value={getCurrentSize()}
                                options={TEXT_SIZES}
                                onChange={onChangeTextSize}
                            />
                        </PanelBody>
                    </InspectorControls>
                </Fragment>
            );
        };
    }, 'addTextSizeControl');

    addFilter(
        'editor.BlockEdit',
        'global-text-size/text-size-control',
        addTextSizeControl
    );
})(window.wp);
