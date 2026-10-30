// Usuarios de la cuenta: solo consulta. Con ellos se inicia sesión en el equipo.
const { tabla } = require('./catalogo');
const { html } = require('../util');

module.exports = {
  consultar(p) {
    return tabla(p, {
      tablas: ['usuarios'],
      campos: '*',
      desde: 'usuarios',
      buscarEn: "Nombre || ' ' || Primer_Apellido || ' ' || Segundo_Apellido || ' ' || Correo",
      columnas: { Nombre: 'Nombre', Correo: 'Correo', Tipo_Usuario: 'Tipo_Usuario' },
      porDefecto: 'Nombre',
      fila: (r) => {
        const permisos = String(r.Permisos || '').split('~').filter(Boolean)
          .filter((m) => m.split(',')[1] === '1').map((m) => m.split(',')[0]);
        const admin = String(r.Tipo_Usuario) === '1';
        return {
          ID: String(r.ID_Usuario),
          Nombre: html(`${r.Nombre} ${r.Primer_Apellido} ${r.Segundo_Apellido}`),
          Correo: html(r.Correo),
          Tipo_Usuario: admin ? '<span class="badge rounded-pill bg-primary">Administrador</span>' : '<span class="badge rounded-pill bg-secondary">Usuario</span>',
          Permisos: admin ? 'Todos' : (permisos.length ? permisos.map(html).join(', ') : '<span class="text-muted">Ninguno</span>')
        };
      }
    });
  }
};
