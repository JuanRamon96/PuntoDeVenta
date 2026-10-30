// Port de c_cajas.php para la única caja enlazada al equipo.
const db = require('../db');
const sesion = require('../sesion');
const turno = require('../turno');
const { ahora, html, uuid } = require('../util');

module.exports = {
  consultar() {
    const caja = db.existeTabla('cajas') ? db.uno('SELECT * FROM cajas WHERE ID_Caja = ?', [turno.cajaId()]) : null;
    if (!caja) {
      return JSON.stringify({});
    }

    const yo = sesion.id();
    const abierta = turno.abierta();
    const enUso = turno.usuarioEnCaja();
    let estado;
    let detalles = String(caja.Detalles || '').trim() !== '' ? `<b>Detalles: </b><span>${html(caja.Detalles)}</span><br>` : '';
    let tomar = '';

    if (!abierta) {
      estado = '<span class="badge rounded-pill bg-danger">Cerrada</span>';
      detalles += `<br><button type="button" class="btn btn-primary btn-sm bAbrirCaja" attrID="${caja.ID_Caja}">Abrir</button>`;
    } else {
      const t = turno.actual();
      const nombre = (id) => {
        const u = db.uno('SELECT Nombre, Primer_Apellido, Segundo_Apellido FROM usuarios WHERE ID_Usuario = ?', [id]);
        return u ? `${u.Nombre} ${u.Primer_Apellido} ${u.Segundo_Apellido}` : '';
      };
      estado = '<span class="badge rounded-pill bg-success">Abierta</span>';
      detalles += `<br><b>Activo: </b><span style="font-size: 18px;">${html(enUso ? nombre(enUso) : 'Ninguno')}</span><br>`;
      detalles += `<b>Abrió caja: </b><span>${html(nombre(t.FK_Usuario_Abrir))}</span><br>
        <b>Apertura: </b><span class="dinero" style="font-size: 26px;">${t.Monto_Abrir}</span>`;
      if (enUso === 0 || enUso === yo) {
        tomar = `<br><button type="button" class="btn btn-primary btn-sm bTomarCaja" attrID="${caja.ID_Caja}">Tomar Caja</button>`;
      }
    }

    return JSON.stringify({
      data: [{
        ID: caja.ID_Caja,
        Nombre: `<span style="font-size: 14px;">${html(caja.Nombre)}</span>`,
        Estado: estado,
        Detalles: detalles + tomar,
        Sucursal: '',
        Acciones: ''
      }],
      totales: { NumRows: 1 }
    });
  },

  insertar(p) {
    if (p.tipo !== 'abrir') {
      return 'Esta función solo está disponible en el sistema web.';
    }
    if (turno.abierta()) {
      return 'La caja ya está abierta';
    }
    const enlace = db.ajuste('enlace');
    db.ejecutar('INSERT INTO detalles_caja (UUID, Cuenta_Origen, FK_Caja, Fecha_Abrir, Monto_Abrir, FK_Usuario_Abrir) VALUES (?, ?, ?, ?, ?, ?)',
      [uuid(), Number(db.ajuste('cuentaDatos') || enlace.cuenta), turno.cajaId(), ahora(), Number(String(p.montoCaja).replace(/,/g, '')) || 0, sesion.id()]);
    turno.tomar(sesion.id());
    return 'Correcto';
  },

  modificar(p) {
    if (p.tipo === 'tomarCaja') {
      turno.tomar(sesion.id());
      return 'Correcto';
    }
    return 'Esta función solo está disponible en el sistema web.';
  }
};
