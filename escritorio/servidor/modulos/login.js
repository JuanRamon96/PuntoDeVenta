// Inicio de sesión con los usuarios que se bajaron del servidor (misma contraseña que en la web).
const bcrypt = require('bcryptjs');
const db = require('../db');
const sesion = require('../sesion');

module.exports = {
  entrar(p) {
    if (!db.existeTabla('usuarios')) {
      return 'No existe';
    }
    const usuario = db.uno('SELECT * FROM usuarios WHERE lower(Correo) = lower(?)', [String(p.correo).trim()]);
    if (!usuario) {
      return 'No existe';
    }
    // PHP guarda los hash como $2y$; bcryptjs entiende el mismo formato como $2a$/$2b$
    const hash = String(usuario.Contrasena).replace(/^\$2y\$/, '$2b$');
    if (!bcrypt.compareSync(String(p.contrasena), hash)) {
      return '0';
    }
    sesion.iniciar(usuario);
    return 'Correcto';
  },

  // Cerrar sesión (metodo=eliminar&accion=login)
  eliminar() {
    sesion.cerrar();
    return 'Correcto';
  }
};
