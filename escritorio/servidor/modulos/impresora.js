// Configuración de la impresora de tickets (accion=impresora): impresora por defecto, ancho del papel y escala.
const db = require('../db');

const ESCALA_SUGERIDA = { 58: 44, 80: 63 }; // 63% es la escala que usa la web para papel de 80 mm

let opciones = null;

function config() {
  const c = db.ajuste('impresora') || {};
  const ancho = [58, 80].includes(Number(c.ancho)) ? Number(c.ancho) : 80;
  return {
    nombre: c.nombre || '',
    ancho,
    escala: Math.min(150, Math.max(20, Number(c.escala) || ESCALA_SUGERIDA[ancho])),
    copias: Math.min(5, Math.max(1, parseInt(c.copias, 10) || 1))
  };
}

module.exports = {
  iniciar(o) {
    opciones = o;
  },
  config,

  async detalles(p) {
    if (p.tipo === 'configuracion') {
      let impresoras = [];
      try {
        impresoras = (await opciones.impresoras()).map((i) => ({ nombre: i.name, etiqueta: i.displayName || i.name, porDefecto: !!i.isDefault }));
      } catch (e) {
        // sin impresoras instaladas
      }
      return JSON.stringify({ config: config(), impresoras, sugerida: ESCALA_SUGERIDA });
    }
    if (p.tipo === 'probar') {
      try {
        await opciones.imprimir('app://pos/controladores/pdf/ticketPrueba.php');
        return 'Correcto';
      } catch (e) {
        return 'Offline~' + e.message;
      }
    }
    return 'Acción desconocida';
  },

  modificar(p) {
    const ancho = Number(p.ancho) === 58 ? 58 : 80;
    db.ajuste('impresora', {
      nombre: String(p.nombre || ''),
      ancho,
      escala: Math.min(150, Math.max(20, Number(p.escala) || ESCALA_SUGERIDA[ancho])),
      copias: Math.min(5, Math.max(1, parseInt(p.copias, 10) || 1))
    });
    return 'Correcto';
  }
};
