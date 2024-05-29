import { useEffect, useState } from 'react';
import axios from 'axios';

const UseUserProfile = (baseUrl, userToken) => {
  const [username, setUsername] = useState('');

  useEffect(() => {
    const fetchProfile = async () => {
      try {
        const response = await axios.get(`${baseUrl}/api/user/profile`, {
          headers: {
            'Authorization': `Bearer ${userToken}`
          }
        });

        if (response.status === 200) {
          setUsername(response.data.pseudo);
        } else {
          console.error('Failed to fetch user profile');
        }
      } catch (error) {
        console.error('Failed to fetch user profile:', error.message);
      }
    };

    if (userToken) {
      fetchProfile();
    }
  }, [userToken, baseUrl]);

  return username;
};

export default UseUserProfile;
