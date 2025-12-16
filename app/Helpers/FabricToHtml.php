<?php

namespace App\Helpers;

class FabricToHtml
{
    public static function render(array $json): string
    {
        $html = '<div class="fabric-wrapper">
        <div class="fabric-canvas">';

        // 🎨 BACKGROUND
        if (!empty($json['backgroundImage'])) {
            $bg = $json['backgroundImage'];

            $html .= sprintf(
                '<img src="%s" style="
                    position:absolute;
                    left:0;
                    top:0;
                    width:100%%;
                    height:100%%;
                    z-index:0;
                ">',
                $bg['src']
            );
        }

        foreach ($json['objects'] as $obj) {

            $left  = $obj['left'] ?? 0;
            $top   = $obj['top'] ?? 0;
            $angle = $obj['angle'] ?? 0;

            $scaleX = $obj['scaleX'] ?? 1;
            $scaleY = $obj['scaleY'] ?? 1;

            $transform = "translate(-50%, -50%) rotate({$angle}deg) scale({$scaleX}, {$scaleY})";

            /* ================= TEXT ================= */
            if ($obj['type'] === 'textbox') {

                $fontWeight = $obj['fontWeight'] ?? 'normal';
                $fontStyle  = ($obj['fontStyle'] ?? 'normal') === 'italic' ? 'italic' : 'normal';
                $underline  = !empty($obj['underline']) ? 'underline' : 'none';

                $textAlign  = $obj['textAlign'] ?? 'left';
                $lineHeight = $obj['lineHeight'] ?? 1.2;

                $transform = "rotate({$angle}deg) scale({$scaleX}, {$scaleY})";

                $html .= sprintf(
                    '<div style="
                        position:absolute;
                        left:%spx;
                        top:%spx;
                        width:%spx;
                        height:%spx;
                        transform:%s;
                        transform-origin:left top;
                        font-family:%s;
                        font-size:%spx;
                        font-weight:%s;
                        font-style:%s;
                        text-decoration:%s;
                        color:%s;
                        text-align:%s;
                        line-height:%s;
                        white-space:pre-wrap;
                        z-index:10;
                    ">%s</div>',
                    $left*1.9,
                    $top*1.9,
                    $obj['width']*1.9,
                    $obj['height']*1.9,
                    $transform,
                    $obj['fontFamily'] ?? 'sans-serif',
                    $obj['fontSize']*1.9 ?? 16,
                    $fontWeight,
                    $fontStyle,
                    $underline,
                    $obj['fill'] ?? '#000',
                    $textAlign,
                    $lineHeight,
                    nl2br(htmlspecialchars($obj['text'] ?? ''))
                );
            }

            /* ================= IMAGE ================= */
            if ($obj['type'] === 'image') {

                $width  = ($obj['width'] ?? 0);
                $height = ($obj['height'] ?? 0);

                $transform = "rotate({$angle}deg) scale({$scaleX}, {$scaleY})";

                $html .= sprintf(
                    '<img src="%s" style="
                        position:absolute;
                        left:%spx;
                        top:%spx;
                        width:%spx;
                        height:%spx;
                        transform:%s;
                        transform-origin:left top;
                        z-index:5;
                    ">',
                    $obj['src'],
                    $left*1.9,
                    $top*1.9,
                    $width*1.9,
                    $height*1.9,
                    $transform
                );
            }
        }

        $html .= '</div>
        </div>';

        return $html;
    }
}