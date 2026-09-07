<?php 
namespace App\Http\Controllers; 
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\User; 
use App\Models\LoginHistory;  
use DB;  
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;  
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Input; 
use App\Helpers\Common; 
use Session;
 
class AdminLoginController extends Controller {  

    public function __construct() { 
        $this->middleware('guest')->except('logout'); 
    } 
	
    function getOS() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    } 

    function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }

    function get_device() {
        $tablet_browser = 0;
        $mobile_browser = 0;

        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $tablet_browser++;
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-');

        if (in_array($mobile_ua,$mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
            $mobile_browser++;
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
              $tablet_browser++;
            }
        } 

        if ($tablet_browser > 0) {
           return  'tablet';
        }
        else if ($mobile_browser > 0) {
           return  'mobile';
        }
        else {
           return  'desktop';
        } 
    }

    public function login() {   
        return view('admin.login'); 
    }

    public function loginSubmit(Request $request){ 
        request()->validate([
            'email' => 'required|email',
            'password' => 'required|string|max:255'
        ]); 

        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password, 'user_type' => 'admin'])) {
            $user = Auth::user(); 
            $lastLogin = LoginHistory::where('user_id', $user->id)->orderby('id', 'desc')->first(); 

            $loginHistory = new LoginHistory([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->user_type, 
                'ip_address' => $this->getIp(),
                'browser' => $this->getOS(),
                'device_type' => $this->get_device(),
                'status' => 'login',
                'last_login' => $lastLogin ? $lastLogin->current_login : null,
                'current_login' => date("Y-m-d h:i:s")
            ]);
            $loginHistory->save(); 

            if ($user->user_type == "admin") { 
                return redirect()->route('admin_dashboard'); 
            }
        } else {
            return back()->with('error', 'Credentials not matched with records.');
        }
    }

    public function logout(Request $request){ 
        $user = Auth::user();   
        $loginHistory = LoginHistory::where('user_id', $user->id)
            ->where('ip_address', $this->getIp())
            ->where('browser', $this->getOS())
            ->where('device_type', $this->get_device())
            ->orderBy('id', 'desc')
            ->first();

        if ($loginHistory) {
            $loginHistory->status = 'logout';
            $loginHistory->save();
        } 
        Auth::logout();
        return redirect('/'); 
    }
}
?>
