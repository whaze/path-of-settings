import { Button, Spinner } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const ImageField = ({ id, label, description, button_text, remove_text, value, onChange, error, image_data }) => {
    const [isUploading, setIsUploading] = useState(false);
    const [currentImageData, setCurrentImageData] = useState(image_data || null);

    // Mettre à jour les données d'image quand la valeur change
    useEffect(() => {
        if (value && (!currentImageData || currentImageData.id !== parseInt(value))) {
            // Récupérer les données de la nouvelle image
            fetchImageData(value);
        } else if (!value) {
            // Réinitialiser si pas de valeur
            setCurrentImageData(null);
        }
    }, [value]);

    // Initialiser avec les données existantes
    useEffect(() => {
        if (image_data) {
            setCurrentImageData(image_data);
        }
    }, [image_data]);

    const fetchImageData = async (attachmentId) => {
        try {
            const response = await fetch(`/wp-json/wp/v2/media/${attachmentId}`);
            if (response.ok) {
                const mediaData = await response.json();
                const imageData = {
                    id: mediaData.id,
                    url: mediaData.media_details?.sizes?.medium?.source_url || mediaData.source_url,
                    width: mediaData.media_details?.sizes?.medium?.width || mediaData.media_details?.width,
                    height: mediaData.media_details?.sizes?.medium?.height || mediaData.media_details?.height,
                    alt: mediaData.alt_text || '',
                    title: mediaData.title?.rendered || '',
                    filename: mediaData.slug || '',
                };
                setCurrentImageData(imageData);
            }
        } catch (error) {
            console.error('Error fetching image data:', error);
        }
    };

    const openMediaUploader = () => {
        if (typeof wp === 'undefined' || !wp.media) {
            console.error('WordPress media library not available');
            return;
        }

        const mediaUploader = wp.media({
            title: label,
            button: {
                text: button_text || __('Select Image', 'path-of-settings')
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        mediaUploader.on('select', () => {
            setIsUploading(true);
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            
            // Mettre à jour immédiatement avec les données de l'attachment
            const newImageData = {
                id: attachment.id,
                url: attachment.sizes?.medium?.url || attachment.url,
                width: attachment.sizes?.medium?.width || attachment.width,
                height: attachment.sizes?.medium?.height || attachment.height,
                alt: attachment.alt || '',
                title: attachment.title || '',
                filename: attachment.filename || '',
            };
            
            setCurrentImageData(newImageData);
            onChange(attachment.id);
            setIsUploading(false);
        });

        mediaUploader.open();
    };

    const removeImage = () => {
        setCurrentImageData(null);
        onChange('');
    };

    const hasImage = value && currentImageData;

    return (
        <div className="pos-field pos-field-image">
            <label className="components-base-control__label">
                {label}
            </label>
            
            {description && (
                <p className="components-base-control__help">
                    {description}
                </p>
            )}

            {error && (
                <div className="components-notice is-error">
                    <div className="components-notice__content">
                        {error}
                    </div>
                </div>
            )}

            <div className="pos-image-field-content">
                {hasImage ? (
                    <div className="pos-image-preview">
                        <div className="pos-image-container">
                            <img 
                                src={currentImageData.url} 
                                alt={currentImageData.alt || label}
                                className="pos-preview-image"
                            />
                            <div className="pos-image-overlay">
                                <div className="pos-image-info">
                                    <span className="pos-image-dimensions">
                                        {currentImageData.width} × {currentImageData.height}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div className="pos-image-actions">
                            <Button 
                                isSecondary 
                                onClick={openMediaUploader}
                                disabled={isUploading}
                            >
                                {__('Change Image', 'path-of-settings')}
                            </Button>
                            <Button 
                                isDestructive 
                                onClick={removeImage}
                                disabled={isUploading}
                            >
                                {remove_text || __('Remove Image', 'path-of-settings')}
                            </Button>
                        </div>
                    </div>
                ) : (
                    <div className="pos-image-placeholder">
                        {isUploading ? (
                            <div className="pos-uploading">
                                <Spinner />
                                <p>{__('Loading image...', 'path-of-settings')}</p>
                            </div>
                        ) : (
                            <div className="pos-no-image">
                                <div className="pos-upload-icon">📷</div>
                                <Button 
                                    isPrimary 
                                    onClick={openMediaUploader}
                                >
                                    {button_text || __('Select Image', 'path-of-settings')}
                                </Button>
                                <p className="pos-upload-help">
                                    {__('Choose an image from your media library', 'path-of-settings')}
                                </p>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
};

export default ImageField;