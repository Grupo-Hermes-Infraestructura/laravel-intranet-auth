<?php

namespace Ghi\IntranetAuth;

trait AuthenticatesIntranetUsers
{
    /**
     * Nombre del campo que se usa para generar el LoginAttemptKey
     * para uso del Login Throttle
     *
     * @var string
     */
    protected $username = 'usuario';

    /**
     * Ruta donde se muestra el formulario para login con la intranet
     *
     * @var string
     */
    protected $loginPath = '/auth/login';

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        if (view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }

        return view('auth.login');
    }

    /**
     * Se ocupa de la peticion de logueo con la intranet para la aplicacion.
     *
     * @param  Request $request
     * @return Response
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'usuario' => 'required', 'clave' => 'required',
        ]);

        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only('usuario', 'clave');

        if (auth()->attempt($credentials, $request->has('remember_me'))) {
            if ($throttles) {
                $this->clearLoginAttempts($request);
            }

            flash("Bienvenido " . auth()->user()->nombre . "!");

            return redirect($this->redirectPath());
        }

        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only('usuario', 'remember_me'))
            ->withErrors([
                'usuario' => $this->getFailedLoginMessage(),
            ]);
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return 'El nombre de usuario o contraseña son invalidos.';
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        Auth::logout();
        Session::flush();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }
}
