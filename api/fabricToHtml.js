export function fabricToHtml(json) {
    let html = `
      <div class="fabric-wrapper">
        <div class="fabric-canvas">
    `
  
    // 🎨 BACKGROUND
    if (json.backgroundImage && json.backgroundImage.src) {
      html += `
        <img src="${json.backgroundImage.src}" style="
          position:absolute;
          left:0;
          top:0;
          width:100%;
          height:100%;
          z-index:0;
        ">
      `
    }
  
    for (const obj of json.objects || []) {
      const left = obj.left ?? 0
      const top = obj.top ?? 0
      const angle = obj.angle ?? 0
      const scaleX = obj.scaleX ?? 1
      const scaleY = obj.scaleY ?? 1
  
      /* ================= TEXT ================= */
      if (obj.type === 'textbox') {
        const fontWeight = obj.fontWeight ?? 'normal'
        const fontStyle = obj.fontStyle === 'italic' ? 'italic' : 'normal'
        const underline = obj.underline ? 'underline' : 'none'
        const textAlign = obj.textAlign ?? 'left'
        const lineHeight = obj.lineHeight ?? 1.2
  
        const transform = `rotate(${angle}deg) scale(${scaleX}, ${scaleY})`
  
        const text = (obj.text || '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/\n/g, '<br>')
  
        html += `
          <div style="
            position:absolute;
            left:${left * 1.9}px;
            top:${top * 1.9}px;
            width:${(obj.width ?? 0) * 1.9}px;
            height:${(obj.height ?? 0) * 1.9}px;
            transform:${transform};
            transform-origin:left top;
            font-family:${obj.fontFamily ?? 'sans-serif'};
            font-size:${(obj.fontSize ?? 16) * 1.9}px;
            font-weight:${fontWeight};
            font-style:${fontStyle};
            text-decoration:${underline};
            color:${obj.fill ?? '#000'};
            text-align:${textAlign};
            line-height:${lineHeight};
            white-space:pre-wrap;
            z-index:10;
          ">${text}</div>
        `
      }
  
      /* ================= IMAGE ================= */
      if (obj.type === 'image') {
        const width = obj.width ?? 0
        const height = obj.height ?? 0
        const transform = `rotate(${angle}deg) scale(${scaleX}, ${scaleY})`
  
        html += `
          <img src="${obj.src}" style="
            position:absolute;
            left:${left * 1.9}px;
            top:${top * 1.9}px;
            width:${width * 1.9}px;
            height:${height * 1.9}px;
            transform:${transform};
            transform-origin:left top;
            z-index:5;
          ">
        `
      }
    }
  
    html += `
        </div>
      </div>
    `
  
    return html
  }  