<template>
  <!-- ============================================================ -->
  <!-- INICIO DE SESIÓN Y REGISTRO                                  -->
  <!-- ============================================================ -->

  <div class="login-section">

    <div class="login-container">

      <!-- ============================================================ -->
      <!-- TÍTULO                                                         -->
      <!-- ============================================================ -->
      <div class="login-header">
        <h2>Iniciar Sesión</h2>
        <p>Accede a tu cuenta de Nexus Hub</p>
      </div>

      <!-- ============================================================ -->
      <!-- FORMULARIO PARA INICIAR SESIÓN                               -->
      <!-- ============================================================ -->
      <div class="login-form-box">

        <form @submit.prevent="handleLogin">

          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" v-model="email" required placeholder="tu@email.com">
          </div>

          <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" v-model="password" required placeholder="Tu contraseña">
          </div>

          <p v-if="error" style="color: #dc3545; margin-bottom: 10px;">{{ error }}</p>

          <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="login-links">
          <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
          <a href="#" class="register-link" @click.prevent="showRegister = true">¿No tienes cuenta? Regístrate</a>
        </div>

      </div>

      <!-- ============================================================ -->
      <!-- FORMULARIO DE REGISTRO (aparece al hacer clic en "Regístrate")-->
      <!-- ============================================================ -->
      <div v-if="showRegister" class="login-form-box" style="margin-top: 20px;">

        <h3>Registro</h3>

        <form @submit.prevent="handleRegister">

          <div class="form-group">
            <label for="reg-username">Nombre de Invocador</label>
            <input type="text" id="reg-username" v-model="regUsername" required placeholder="Tu nombre de invocador">
          </div>

          <div class="form-group">
            <label for="reg-email">Correo Electrónico</label>
            <input type="email" id="reg-email" v-model="regEmail" required placeholder="tu@email.com">
          </div>

          <div class="form-group">
            <label for="reg-password">Contraseña</label>
            <input type="password" id="reg-password" v-model="regPassword" required placeholder="Tu contraseña (mín. 6 caracteres)">
          </div>

          <p v-if="regError" style="color: #dc3545; margin-bottom: 10px;">{{ regError }}</p>

          <button type="submit" class="btn-login">Registrarse</button>
          <button type="button" class="btn-login" style="background: #666; margin-left: 10px;" @click="showRegister = false">Cancelar</button>

        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoginView',

  // ============================================================
  // DATOS
  // ============================================================
  data() {
    return {
      email: '',
      password: '',
      error: '',              // Mensaje de error si algo sale mal

      showRegister: false,    // true = muestra el formulario de registro

      regUsername: '',
      regEmail: '',
      regPassword: '',
      regError: ''            // Mensaje de error del registro
    };
  },

  // ============================================================
  // FUNCIONES
  // ============================================================
  methods: {

    // ----------------------------------------------------------
    // handleLogin: inicia sesión con el servidor
    // ----------------------------------------------------------
    async handleLogin() {
      this.error = '';

      try {
        // Envía email y contraseña al servidor
        const response = await fetch('api/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: this.email, password: this.password })
        });
        const data = await response.json();

        if (data.success) {
          // Guarda los datos del usuario en el navegador
          localStorage.setItem('nexus_user', JSON.stringify(data.user));
          // Avisa al padre que ya inició sesión
          this.$emit('login-success');
        } else {
          this.error = data.error;
        }
      } catch (e) {
        this.error = 'Error de conexión. Verifica que el servidor esté activo.';
      }
    },

    // ----------------------------------------------------------
    // handleRegister: registra un usuario nuevo
    // ----------------------------------------------------------
    async handleRegister() {
      this.regError = '';

      if (this.regPassword.length < 6) {
        this.regError = 'La contraseña debe tener al menos 6 caracteres';
        return;
      }

      try {
        // Envía los datos del registro al servidor
        const response = await fetch('api/register.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            username: this.regUsername,
            email: this.regEmail,
            password: this.regPassword
          })
        });
        const data = await response.json();

        if (data.success) {
          localStorage.setItem('nexus_user', JSON.stringify(data.user));
          this.$emit('login-success');
        } else {
          this.regError = data.error;
        }
      } catch (e) {
        this.regError = 'Error de conexión. Verifica que el servidor esté activo.';
      }
    }
  }
};
</script>
