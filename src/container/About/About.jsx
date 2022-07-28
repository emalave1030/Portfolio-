import React, {useState, useEffect} from 'react'
import { motion } from 'framer-motion';
import { AppWrap, MotionWrap } from '../../wrapper';


import './About.scss';
import { urlFor, client } from '../../client';

const About = () => {

  const [abouts, setAbouts] = useState([]);

  useEffect(() => {
    const query = '*[_type == "abouts"]';

    client.fetch(query)
    .then((data) => { setAbouts(data); })
  }, []);
  

  return (
    <>
    <h2 className="head-text">
      Good Design <span>and clean sustainable  </span>code <br/>
      <span>are key in every project.</span>
      </h2>
      <p>I am an inquisitive Full Stack/Web3 developer specializing in Front-End development. I use my savviness in design, code, and what I've picked up working close with social media teams to help take your business to the next level on a virtual platform. I love to collaborate on projects and create jaw-dropping websites with code.</p>
      <div className="app__profiles">
      {abouts.map((about, index) => (
        <motion.div
        whileInView={{ opacity: 1}}
        whileHover={{ scale: 1.1 }}
        transition={{ duration: 0.5, type: 'tween' }}
        className="app__profile-item"
        key={about.title + index}
        >
          <img src={urlFor(about.imgUrl)} alt={about.title}/>
          <h2 className="bold-text" style={{ marginTop: 20}}>{about.title}</h2>
          <p className="p-text" style={{ marginTop: 10}}>{about.description}</p>
        </motion.div>
      ))}
      </div>
      </>
  )
}

export default AppWrap(
  MotionWrap(About, 'app__about'),
  'about',
  'app__whitebg',
);