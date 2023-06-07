<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

class System{
    private $CI;    

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('System_model');
    }   
    
    public function check_access($url = null, $action = null)
    {
        $user_id = $this->CI->session->userdata('id_u');
        $access_user_id = [1, 3, 14, 17];
        if(in_array($user_id, $access_user_id))
        {
            return TRUE;
        }
        else
        {
            if($url != null)
            {
                $module = $this->CI->System_model->get_module($url);
                if(isset($module))
                {
                    $access = $this->CI->System_model->get_access($user_id, $module['url']);
                    if(isset($access))
                    {
                        $data = $access->row_array();
                        if(in_array($action, json_decode($access['method'])))
                        {
                            return TRUE;
                        }               
                        else
                        {
                            return FALSE;                        
                        }         
                    }
                    else
                    {
                        return FALSE;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {                
                // $uri1 = $this->CI->uri->segment(1); $uri2 = $this->CI->uri->segment(2);
                // if($uri1 == "" || $uri1 == "dashboard")
                // {
                //     return TRUE;
                // }
                // else if($uri1 == "master" || $uri1 == "transaction" || $uri1 == "finance" || $uri1 == "report")
                // {                
                //     return TRUE;
                // }
                // else if($uri2 == "return")
                // {
                //     $url = $uri1.'/'.$uri2;
                // }
                // else
                // {
                //     $url = $uri1;                                 
                // }                
                // $module = $this->CI->System_model->get_module($url);
                // if(isset($module))
                // {
                //     $access = $this->CI->System_model->get_access($user_id, $module['url']);
                //     if(isset($access))
                //     {
                //         $data = $access->row_array();
                //         if($data[$action] == 1)
                //         {
                //             return TRUE;
                //         }               
                //         else
                //         {
                //             return FALSE;                        
                //         }         
                //     }
                //     else
                //     {
                //         return FALSE;
                //     }
                // }
                // else
                // {
                //     return false;
                // } 
                return true;
            }
        }            
    }    
}