import dayjs from "dayjs";
import axios from "axios";
import Quill from 'quill'
import { saveAs } from 'file-saver'
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

export async function requestFile(token, key) {
  axios.defaults.headers.common = {
    Authorization: `bearer ${token}`,
  }

  const res = await axios.get(route('api.file', {
    _query: {
      key: key
    }
  }), {
    headers: {
      Authorization: `Bearer ${token}`,
    },
    responseType: 'blob',
  })

  return res.data
}

export async function requestFilePreview(token, key) {
  axios.defaults.headers.common = {
    Authorization: `bearer ${token}`,
  }

  try {
    const res = await axios.get(route('api.file_preview', {
      _query: {
        key: key
      }
    }), {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    return res
  } catch (err) {
    console.error(err)
    return ''
  }
}

export function convertDeltaContent(delta) {
    delta.ops.forEach(({ insert }, index, arr) => {
        if (Object.prototype.toString.call(insert) === "[object String]")
            arr[index] = {
                insert
            }
        else
            arr[index] = {
                insert: "\n"
            }
    })

  const cont = document.createElement('div');
  (new Quill(cont)).setContents(delta);
  return cont.getElementsByClassName('ql-editor')[0].innerHTML
}

export function downloadFile(token, file) {
    requestFile(token, file).then((url) => {
        saveAs(url)
    }).catch(console.error)
}
export function getFileName(file) {
  const arr = file.split('/')
  const len = arr.length

  return arr[len - 1]
}

export async function keyToJpeg(token, key) {
  const response = await requestFile(token, key)
  const url = URL.createObjectURL(response)
  return url
}
