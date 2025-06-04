<?php

namespace PathOfSettings\Fields;

use PathOfSettings\Core\Contracts\FieldInterface;

class ImageField implements FieldInterface {

    private string $id;
    private array $config;
    private $value = '';

    public function __construct( string $id, array $config = [] ) {
        $this->id = $id;
        $this->config = wp_parse_args(
            $config,
            [
                'label'       => '',
                'description' => '',
                'default'     => '',
                'button_text' => __( 'Select Image', 'path-of-settings' ),
                'remove_text' => __( 'Remove Image', 'path-of-settings' ),
                'multiple'    => false,
                'file_type'   => 'image',
                'required'    => false,
            ]
        );

        $this->value = $this->config['default'];
    }

    public function getId(): string {
        return $this->id;
    }

    public function getType(): string {
        return 'image';
    }

    public function getConfig(): array {
        return $this->config;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue( $value ): self {
        $this->value = absint( $value );
        return $this;
    }

    public function validate( $value ) {
        if ( $this->config['required'] && empty( $value ) ) {
            return new \WP_Error(
                'required_field',
                sprintf( __( 'The field "%s" is required.', 'path-of-settings' ), $this->config['label'] )
            );
        }

        // Valider que c'est un ID d'attachment valide
        if ( ! empty( $value ) && ! wp_attachment_is_image( $value ) ) {
            return new \WP_Error(
                'invalid_image',
                sprintf( __( 'Invalid image for field "%s".', 'path-of-settings' ), $this->config['label'] )
            );
        }

        return true;
    }

    public function sanitize( $value ) {
        return absint( $value );
    }

    public function toArray(): array {
        $data = [
            'id'     => $this->id,
            'type'   => $this->getType(),
            'config' => $this->config,
            'value'  => $this->value,
        ];

        // Ajouter les données de l'image si une valeur existe
        if ( ! empty( $this->value ) ) {
            $attachment_id = absint( $this->value );
            
            // Vérifier que l'attachment existe
            if ( wp_attachment_is_image( $attachment_id ) ) {
                $attachment = wp_get_attachment_image_src( $attachment_id, 'medium' );
                
                if ( $attachment ) {
                    $data['image_data'] = [
                        'id'       => $attachment_id,
                        'url'      => $attachment[0],
                        'width'    => $attachment[1],
                        'height'   => $attachment[2],
                        'alt'      => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
                        'title'    => get_the_title( $attachment_id ),
                        'filename' => basename( get_attached_file( $attachment_id ) ),
                        'filesize' => size_format( filesize( get_attached_file( $attachment_id ) ) ),
                    ];
                }
            }
        }

        return $data;
    }
}