import animate from "https://cdn.jsdelivr.net/npm/animateplus@2/animateplus.js";

const palette = [
  "616AFF",
  "2DBAE7",
  "FFBF00",
  "48DC6B",
  "DBDEEA",
  "FC6E3F"
];

const random = (min, max) =>
  Math.random() * (max - min) + min;

const randomColor = () =>
  `#${palette[Math.floor(random(0, palette.length))]}`;

const createElements = (total, elements = [], wrap = document.createDocumentFragment()) => {
  if (!total) {
    document.body.appendChild(wrap);
    return elements;
  }
  const element = document.createElement("span");
  element.style.left = `${random(-3, 99)}%`;
  element.style.background = randomColor();
  elements.push(element);
  wrap.appendChild(element);
  return createElements(total - 1, elements, wrap);
};

const elements = createElements(500);
const {innerHeight: y} = window;

animate({
  elements,
  duration: 10000,
  delay: index => index * 20,
  easing: "linear",
  loop: true,
  transform: ["translateY(40px) scale(1) rotate(0deg)", `-${y} 0 720`]
});
