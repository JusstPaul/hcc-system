import dayjs from "dayjs";
import axios from "axios";
import { h } from "vue";
import { NIcon } from "naive-ui";
import { Inertia } from "@inertiajs/inertia";

export function logout() {
  Inertia.post(route("post.logout"));
}

export function renderIcon(icon) {
  return () => h(NIcon, null, {
    default: () => h(icon),
  });
}

export function formatName(lName, mName, fName) {
  if (mName == null || mName.length === 0) {
    return `${lName}, ${fName}`;
  }
  return `${lName}, ${fName} ${mName[0]}.`;
}

export function formatSchoolYear(schoolYear) {
  return schoolYear == null
    ? "None"
    : `${schoolYear.start} to ${schoolYear.end}`;
}

export function formatTime(time) {
  return dayjs(time).format("hh:mm A");
}

export function allowNumberOnly(input) {
  return !input || /^\d+$/.test(input);
}


export function requestFile(token, key, onLoad = (url) => { }) {

  axios.defaults.headers.common = {
    Authorization: `bearer ${token}`,
  }

  axios.get(route('api.file', {
    _query: {
      key: key
    }
  }), {
    headers: {
      Authorization: `Bearer ${token}`,
    },
    responseType: 'blob',
  }).then((res) => {
    const reader = new FileReader()
    reader.readAsDataURL(res.data)
    reader.onload = () => {
      const result = reader.result.toString();
      onLoad(result ? result : '#')
    }
  }).catch((err) => {
    console.error(err)
  })

}
